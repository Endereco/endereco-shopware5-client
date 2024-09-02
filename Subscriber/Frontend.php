<?php

namespace EnderecoShopware5Client\Subscriber;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\CacheManager;
use Shopware\Components\Logger;
use Shopware\Models\Country\Country;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\AddressRepository;
use Shopware_Controllers_Backend_Config;

class Frontend implements SubscriberInterface
{
    /**
     * @var string
     */
    private $pluginDir;
    private $pluginInfo;
    private $logger;
    private $http;
    private $enderecoService;
    private $config;
    private $themeName;
    private $registerSubmitData;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * Payment methods used to recognize an existing customer.
     */
    private $defaultPaymentMethodsWhitelist = [
        'prepayment',
        'cash',
        'invoice',
        'debit',
        'sepa'
    ];

    /**
     * Payment methods used to recognize a paypal checkout customer.
     */
    private $paypalExpressCheckoutPaymentMethodsWhitelist = [
        'swagpaymentpaypalunified'
    ];

    /**
     * @param string $pluginDir
     */
    public function __construct($pluginDir, $pluginInfo, $logger, $cacheManager, $enderecoService)
    {
        $this->pluginDir = $pluginDir;
        $this->pluginInfo = $pluginInfo;
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
        $this->http = new \GuzzleHttp\Client(['timeout' => 3.0, 'connection_timeout' => 2.0]);
        $this->enderecoService = $enderecoService;

        $shop = false;
        if (Shopware()->Container()->initialized('shop')) {
            $shop = Shopware()->Container()->get('shop');
        }
        if (!$shop) {
            $shop = Shopware()->Container()->get('models')
                ->getRepository(\Shopware\Models\Shop\Shop::class)
                ->getActiveDefault();
        }

        $this->themeName = strtolower($shop->getTemplate()->getTemplate());
        $this->config = Shopware()->Container()
            ->get('shopware.plugin.cached_config_reader')
            ->getByPluginName('EnderecoShopware5Client', $shop);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected' => 'onCollectTemplateDir',

            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Config' => 'onPostDispatchConfig',

            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Register' => 'sendDoAccountingRegister',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Address' => 'sendDoAccountingAddress',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Forms' => 'sendDoAccountingForms',

            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'checkAdressesOrOpenModals',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Account' => 'onAccountPostDispatch',

            'Enlight_Controller_Action_PreDispatch_Frontend_Register' => 'beforeRegister',
            'Shopware_Modules_Admin_SaveRegister_Successful' => 'afterRegister',
            'Shopware_Modules_Admin_SaveRegister_DoubleOptIn_Waiting'
                => 'afterRegister', // This option appears in SW 5.4

            'Shopware_Modules_Order_SaveOrder_FilterParams' => 'addCommentToOrder',
        ];
    }

    /**
     * Handles actions after a successful customer registration.
     *
     * This method is triggered after the customer registration process is completed successfully.
     * It retrieves the customer's attribute model based on the customer ID and updates the profile
     * and email attributes from the registration data. The updated attributes are then persisted
     * in the database. Thats pretty much the only way to save those attributes we could find, that
     * would work in older versions (e.g. 5.3) too.
     *
     * @param \Enlight_Event_EventArgs $args The event arguments passed to the listener,
     *                                       containing the customer ID and other relevant data.
     *
     * @return void
     */
    public function afterRegister(\Enlight_Event_EventArgs $args)
    {
        $customerId = $args->get('id');

        /**
         * @var Shopware\Models\Attribute\Customer
         */
        $attributeModel = $this->getCustomerAttributeModelFromId($customerId);

        $this->setProfileAttributesFromBilling(
            $attributeModel,
            $this->registerSubmitData['register']['billing']['attribute']
        );
        $this->setEmailAttributes($attributeModel, $this->registerSubmitData['email']['attribute']);
        $this->registerSubmitData = null;

        Shopware()->Models()->persist($attributeModel);
        Shopware()->Models()->flush();
    }

    /**
     * Prepares customer registration data before finalizing the registration process.
     *
     * This method is called before the customer registration process is finalized. Basically
     * before the action is processed we save here in this object the passed formData, to use it
     * later (after the customer is registered and persisted) to add and save the attributes.
     *
     * @param \Enlight_Event_EventArgs $args The event arguments passed to the listener,
     *                                       containing the request and action details.
     *
     * @return void
     */
    public function beforeRegister(\Enlight_Event_EventArgs $args)
    {
        $request = $args->getSubject()->Request();
        $actionName = $request->getActionName();

        if ($actionName !== 'saveRegister') {
            return;
        }

        $request = Shopware()->Front()->Request();
        $this->registerSubmitData = $request->getPost();
    }

    /**
     * This method ensures that on profile page we actually have attributes, as shopware 5 itself doesn't do it.
     * Its also responsible for saving those attributes upon submit.
     *
     * @param \Enlight_Event_EventArgs $args Event arguments containing the controller and request information.
     */
    public function onAccountPostDispatch(\Enlight_Event_EventArgs $args)
    {
        $controller = $args->getSubject();
        $request = $controller->Request();
        $view = $controller->View();

        $userData = $view->getAssign('sUserData');
        $formData = $view->getAssign('form_data');
        $actionName = $request->getActionName();

        if ($request->isPost() && ($actionName === 'saveProfile' || $actionName === 'saveEmail')) {
            $this->updateCustomerAttributes($userData, $request->getPost());
        }

        $this->assignFormDataAttributes($userData, $formData);

        $view->assign('sUserData', $userData);
        $view->assign('form_data', $formData);
    }

    /**
     * Updates customer profile attributes for profile and email based on the submitted data.
     * Depending on which data is actually available its either the profile or the email,
     * that gets updated.
     *
     * @param array $userData The current user's data.
     * @param array $submittedData The submitted data from the profile or email form.
     */
    private function updateCustomerAttributes($userData, $submittedData)
    {
        $customerId = $userData['additional']['user']['id'];
        $attributeModel = $this->getCustomerAttributeModelFromId($customerId);

        if (isset($submittedData['profile']['attribute'])) {
            $this->setProfileAttributes($attributeModel, $submittedData['profile']['attribute']);
        }

        if (isset($submittedData['email']['attribute'])) {
            $this->setEmailAttributes($attributeModel, $submittedData['email']['attribute']);
        }

        Shopware()->Models()->persist($attributeModel);
        Shopware()->Models()->flush();
    }

    /**
     * Retrieves or creates the customer attribute model for the given customer ID
     * to persist attributes to the database.
     *
     * @param int $customerId The ID of the customer.
     *
     * @return \Shopware\Models\Attribute\Customer The customer attribute model.
     */
    private function getCustomerAttributeModelFromId($customerId)
    {
        $attributeRepository = Shopware()->Models()->getRepository('Shopware\Models\Attribute\Customer');
        $attributeModel = $attributeRepository->findOneBy(['id' => $customerId]);

        if (!$attributeModel) {
            $attributeModel = new \Shopware\Models\Attribute\Customer();
            $attributeModel->setUserId($customerId);
        }

        return $attributeModel;
    }

    /**
     * Sets the profile-related attributes on the customer attribute model.
     *
     * @param \Shopware\Models\Attribute\Customer $attributeModel The customer attribute model.
     * @param array $attributes The profile attributes to be set.
     */
    private function setProfileAttributes($attributeModel, $attributes)
    {
        $attributeModel->setEnderecoProfileStatusGh($attributes['enderecoProfileStatusGh']);
        $attributeModel->setEnderecoProfilePredictionsGh($attributes['enderecoProfilePredictionsGh']);
        $attributeModel->setEnderecoProfileHashGh($attributes['enderecoProfileHashGh']);
        $attributeModel->setEnderecoProfileSessionIdGh($attributes['enderecoProfileSessionIdGh']);
        $attributeModel->setEnderecoProfileSessionCounterGh($attributes['enderecoProfileSessionCounterGh']);

        return $attributeModel;
    }

    /**
     * Sets the profile-related attributes on the customer attribute model using the person attributes from
     * billing address.
     *
     * @param \Shopware\Models\Attribute\Customer $attributeModel The customer attribute model.
     * @param array $attributes The profile attributes to be set.
     */
    private function setProfileAttributesFromBilling($attributeModel, $attributes)
    {
        $attributeModel->setEnderecoProfileStatusGh($attributes['enderecoPersonStatusGh']);
        $attributeModel->setEnderecoProfilePredictionsGh($attributes['enderecoPersonPredictionsGh']);
        $attributeModel->setEnderecoProfileHashGh($attributes['enderecoPersonHashGh']);
        $attributeModel->setEnderecoProfileSessionIdGh($attributes['enderecoPersonSessionIdGh']);
        $attributeModel->setEnderecoProfileSessionCounterGh($attributes['enderecoPersonSessionCounterGh']);
    }

    /**
     * Sets the email-related attributes on the customer attribute model.
     *
     * @param \Shopware\Models\Attribute\Customer $attributeModel The customer attribute model.
     * @param array $attributes The email attributes to be set.
     */
    private function setEmailAttributes($attributeModel, $attributes)
    {
        $attributeModel->setEnderecoEmailStatusGh($attributes['enderecoEmailStatusGh']);
        $attributeModel->setEnderecoEmailPredictionsGh($attributes['enderecoEmailPredictionsGh']);
        $attributeModel->setEnderecoEmailHashGh($attributes['enderecoEmailHashGh']);
        $attributeModel->setEnderecoEmailSessionIdGh($attributes['enderecoEmailSessionIdGh']);
        $attributeModel->setEnderecoEmailSessionCounterGh($attributes['enderecoEmailSessionCounterGh']);

        return $attributeModel;
    }

    /**
     * Assigns form data attributes from the user data to the form_data variable in smarty, which
     * makes them accessible in frontend. The way we do it here keeps the format compatible
     * with the includable sdk_meta_field template.
     *
     * @param array $userData The current user's data.
     * @param array &$formData The form data to be updated with user attributes.
     */
    private function assignFormDataAttributes($userData, &$formData)
    {
        $formData['profile']['attribute'] = [
            'enderecoProfileStatusGh' => $userData['additional']['user']['endereco_profile_status_gh'],
            'enderecoProfilePredictionsGh' => $userData['additional']['user']['endereco_profile_predictions_gh'],
            'enderecoProfileHashGh' => $userData['additional']['user']['endereco_profile_hash_gh'],
            'enderecoProfileSessionIdGh' => $userData['additional']['user']['endereco_profile_session_id_gh'],
            'enderecoProfileSessionCounterGh' => $userData['additional']['user']['endereco_profile_session_counter_gh'],
        ];

        $formData['email']['attribute'] = [
            'enderecoEmailStatusGh' => $userData['additional']['user']['endereco_email_status_gh'],
            'enderecoEmailPredictionsGh' => $userData['additional']['user']['endereco_email_predictions_gh'],
            'enderecoEmailHashGh' => $userData['additional']['user']['endereco_email_hash_gh'],
            'enderecoEmailSessionIdGh' => $userData['additional']['user']['endereco_email_session_id_gh'],
            'enderecoEmailSessionCounterGh' => $userData['additional']['user']['endereco_email_session_counter_gh'],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sendDoAccountingForms($args)
    {
        $this->doAccounting();
    }

    public function sendDoAccountingRegister($args)
    {
        $request = $args->getRequest();
        $blackListActions = [
            'ajax_validate_password',
            'ajax_validate_email'
        ];
        if (in_array($request->getActionName(), $blackListActions)) {
            return;
        }
        $this->doAccounting();
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function sendDoAccountingAddress($args)
    {
        $this->doAccounting();
    }

    public function addCommentToOrder($args)
    {
        $sOrder = $args->get('subject');
        $returnValue = $args->getReturn();

        if (!$this->config['isPluginActive']) {
            return $returnValue;
        }

        if (!$this->config['addInternalComment']) {
            return $returnValue;
        }

        $statusCodes = explode(',', $sOrder->sUserData['shippingaddress']['attributes']['enderecoamsstatus']);

        if (!empty($sOrder->sUserData['shippingaddress']['attributes']['enderecoamsapredictions'])) {
            $predictions = json_decode(
                $sOrder->sUserData['shippingaddress']['attributes']['enderecoamsapredictions'],
                true
            );
        } else {
            $predictions = [];
        }

        $curDate = date('d.m.Y H:i:s', time());
        $Snipt = Shopware()->Snippets()->getNamespace('EnderecoShopware5Client');

        // Write internal comment for specific case.
        // Case #1: Address was not found
        if (
            in_array('address_not_found', $statusCodes)
            && !empty($Snipt->get('statusAddressNotFoundMainERP'))
        ) {
            $template = $Snipt->get('statusAddressTimestampCheckERP');
            $commentHeadline = sprintf($template, $curDate);
            $commentBody = $Snipt->get('statusAddressNotFoundMainERP');
            $returnValue['internalcomment'] = implode("\n", [$commentHeadline, $commentBody]);
            return $returnValue;
        }

        // Case #2: Address is correct -- dont save anything
        if (
            in_array('address_correct', $statusCodes)
            && !empty($Snipt->get('statusAddressCorrectMainERP'))
        ) {
            $template = $Snipt->get('statusAddressTimestampCheckERP');
            $commentHeadline = sprintf($template, $curDate);
            $commentBody = $Snipt->get('statusAddressCorrectMainERP');
            $returnValue['internalcomment'] = implode("\n", [$commentHeadline, $commentBody]);
            return $returnValue;
        }

        // Case #3: Address needs correction
        if (
            in_array('address_needs_correction', $statusCodes) &&
            !empty($Snipt->get('statusAddressNeedsCorrectionMainERP'))
        ) {
            $template = $Snipt->get('statusAddressTimestampCheckERP');
            $commentHeadline = sprintf($template, $curDate);
            $commentBody = $Snipt->get('statusAddressNeedsCorrectionMainERP');

            if (
                in_array('building_number_not_found', $statusCodes) &&
                !empty($Snipt->get('statusAddressNeedsCorrectionBuildingNotFoundERP'))
            ) {
                $commentBody .= ' ' . $Snipt->get('statusAddressNeedsCorrectionBuildingNotFoundERP');
            }

            if (
                in_array('building_number_missing', $statusCodes) &&
                !empty($Snipt->get('statusAddressNeedsCorrectionBuildingIsMissingERP'))
            ) {
                $commentBody .= ' ' . $Snipt->get('statusAddressNeedsCorrectionBuildingIsMissingERP');
            }

            if (!empty($predictions[0])) {
                $commentBody .= " " . $Snipt->get('statusAddressNeedsCorrectionSecondaryERP') . " \n";
                $commentCorrection = sprintf(
                    "  %s %s,  %s %s,  %s", // TODO: country specific formats.
                    $predictions[0]['streetName'],
                    $predictions[0]['buildingNumber'],
                    $predictions[0]['postalCode'],
                    $predictions[0]['locality'],
                    strtoupper($predictions[0]['countryCode'])
                );
            } else {
                $commentBody .= " " . $Snipt->get('statusAddressNoPredictions') . " \n";
                $commentCorrection = null;
            }

            $returnValue['internalcomment'] = implode("\n", [$commentHeadline, $commentBody, $commentCorrection]);
            return $returnValue;
        }
        // Case #4: Address has multiple variants
        if (
            in_array('address_multiple_variants', $statusCodes) &&
            !empty($Snipt->get('statusAddressMultipleVariantsMainERP'))
        ) {
            $template = $Snipt->get('statusAddressTimestampCheckERP');
            $commentHeadline = sprintf($template, $curDate);
            $commentBody = $Snipt->get('statusAddressMultipleVariantsMainERP');
            if (0 < count($predictions)) {
                $commentBody .= " " . $Snipt->get('statusAddressMultipleVariantsSecondaryERP') . " \n";
                $variants = [];
                foreach ($predictions as $prediction) {
                    $variants[] = sprintf(
                        "  %s %s,  %s %s,  %s", // TODO: country specific formats.
                        $prediction['streetName'],
                        $prediction['buildingNumber'],
                        $prediction['postalCode'],
                        $prediction['locality'],
                        strtoupper($prediction['countryCode'])
                    );
                }
                $commentCorrection = implode("\n", $variants);
            } else {
                $commentCorrection = null;
            }
            $returnValue['internalcomment'] = implode("\n", [$commentHeadline, $commentBody, $commentCorrection]);
            return $returnValue;
        }

        // Case #5: Address is of not supported type. Should not be commented.
        // Code removed.

        return $returnValue;
    }

    public function checkAdressesOrOpenModals($args)
    {
        if (!$this->config['isPluginActive']) {
            return;
        }

        $request = $args->getRequest();
        $controller = $args->get('subject');
        $view = $controller->View();
        $availableActions = ['confirm'];

        if (!in_array(strtolower($request->getActionName()), $availableActions, true)) {
            return;
        }

        $sUserData = $view->getAssign('sUserData');
        $currentPaymentMethod = $sUserData['additional']['payment']['name'];
        $continue = false;

        // This method makes sure the street is saved in split form in attributes.
        $this->ensureSplitStreet($sUserData);

        /**
         * If existing customers can be checked and the payment method is whitelisted -> check it.
         */
        if (
            $this->config['checkExisting'] &&
            $this->isCurrentPaymentMethodInWhitelist(
                $currentPaymentMethod,
                $this->getExistingCustomerPaymentWhitelist()
            )
        ) {
            $continue = true;
        }

        /**
         * If paypal express check is active and payment method is paypalexpress -> check it.
         */
        if (
            $this->config['checkPayPalExpress'] &&
            $this->isCurrentPaymentMethodInWhitelist(
                $currentPaymentMethod,
                $this->getPayPalExpressCheckoutPaymentWhitelist()
            )
        ) {
            $continue = true;
        }

        /**
         * If none of the conditions above were met, abort the operation.
         */
        if (!$continue) {
            return;
        }

        if (array_key_exists('user', $sUserData['additional'])) {
            // Fetch all user addresses.
            /**
             * @var AddressRepository
             */
            $addressRepository = Shopware()->Models()->getRepository(Address::class);
            $addresses = $addressRepository->getListArray($sUserData['additional']['user']['id']);
            $addressesToCheck = array();

            foreach ($addresses as $address) {
                // Check if users address is alright.
                if (
                    array_key_exists('enderecoamsstatus', $address['attribute']) &&
                    (
                        !array_key_exists('moptwunschpaketaddresstype', $address['attribute']) ||
                        !in_array($address['attribute']['moptwunschpaketaddresstype'], ['filiale', 'packstation'])
                    ) &&
                    (
                        !$address['attribute']['enderecoamsstatus'] ||
                        (false !== strpos($address['attribute']['enderecoamsstatus'], 'address_not_checked'))
                    )
                ) {
                    $addressesToCheck[$address['id']] = true;
                }
            }

            // Check addresses.
            $checkedAddresses = $this->enderecoService->checkAddresses(array_keys($addressesToCheck));

            if (
                $addressesToCheck &&
                (0 < $checkedAddresses) &&
                !Shopware()->Session()->endereco_should_not_reload_anymore
            ) {
                $view->assign('endereco_need_to_reload', true);
                Shopware()->Session()->endereco_should_not_reload_anymore = true;
                return;
            }
        }

        $needToCheckBilling = '';
        $needToCheckShipping = '';

        // Check if users billing address is alright.
        if (
            $sUserData &&
            $sUserData['billingaddress'] &&
            array_key_exists('enderecoamsstatus', $sUserData['billingaddress']['attributes']) &&
            !in_array(
                'address_selected_by_customer',
                explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus'])
            ) &&
            !in_array(
                'address_selected_automatically',
                explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus'])
            ) &&
            (
                in_array(
                    'address_needs_correction',
                    explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus'])
                ) ||
                in_array(
                    'address_multiple_variants',
                    explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus'])
                ) ||
                in_array(
                    'address_not_found',
                    explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus'])
                )
            )
        ) {
            $needToCheckBilling = '1';
        }
        // Check if users billing address is alright.
        if (
            $sUserData &&
            $sUserData['shippingaddress'] &&
            $sUserData['billingaddress']['id'] !== $sUserData['shippingaddress']['id'] &&
            array_key_exists('enderecoamsstatus', $sUserData['shippingaddress']['attributes']) &&
            !in_array(
                'address_selected_by_customer',
                explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus'])
            ) &&
            !in_array(
                'address_selected_automatically',
                explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus'])
            ) &&
            (
                in_array(
                    'address_needs_correction',
                    explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus'])
                ) ||
                in_array(
                    'address_multiple_variants',
                    explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus'])
                ) ||
                in_array(
                    'address_not_found',
                    explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus'])
                )
            )
        ) {
            $needToCheckShipping = '1';
        }

        $view->assign('endereco_need_to_check_billing', $needToCheckBilling);
        $view->assign('endereco_need_to_check_shipping', $needToCheckShipping);
    }

    /**
     * This method iterates through all addresses of the existing user, checking if the full street needs to be split.
     * If it needs to be split, then the spliting request is sent to endereco api.
     * The parts are then saved in attributes.
     *
     * @param array $sUserData Array with the details of the current user.
     *
     * @return void
     */
    private function ensureSplitStreet($sUserData)
    {
        /**
         * @var AddressRepository
         */
        $addressRepository = Shopware()->Models()->getRepository(Address::class);

        /**
         * @var CountryRepository
         */
        $countryRepository = Shopware()->Models()->getRepository(Country::class);

        $addresses = $addressRepository->getListArray($sUserData['additional']['user']['id']);

        foreach ($addresses as $address) {
            // Check if users address is alright.
            $fullStreet = $address['street'];

            if (!empty($countryRepository->find($address['countryId'])->getIso())) {
                $countryCode = strtoupper($countryRepository->find($address['countryId'])->getIso());
            } else {
                $countryCode = 'DE';
            }

            if (
                (strpos($fullStreet, $address['attribute']['enderecostreetname']) === false) ||
                (strpos($fullStreet, $address['attribute']['enderecobuildingnumber']) === false)
            ) {
                list($streetName, $buildingNumber) = $this->enderecoService->splitStreet(
                    $fullStreet,
                    $countryCode
                );

                try {
                    $address = $addressRepository->find($address['id']);
                    $attribute = $address->getAttribute();

                    // Some plugins, like amazon pay, don't create attribute entity, when they create address.
                    // so we check if attribute entity is missing and add it manually.
                    if (!$attribute) {
                        $attribute = new \Shopware\Models\Attribute\CustomerAddress();
                        $address->setAttribute($attribute);
                    }

                    if ($attribute && method_exists($attribute, 'setEnderecostreetname')) {
                        $attribute->setEnderecostreetname($streetName);
                    }

                    if ($attribute && method_exists($attribute, 'setEnderecobuildingnumber')) {
                        $attribute->setEnderecobuildingnumber($buildingNumber);
                    }

                    Shopware()->Container()->get('shopware_account.address_service')->update($address);
                } catch (\Exception $e) {
                    $this->logger->addRecord(Logger::ERROR, $e->getMessage());
                }
            }
        }
    }

    public function onPostDispatchConfig(\Enlight_Event_EventArgs $args)
    {
        /** @var Shopware_Controllers_Backend_Config $subject */
        $subject = $args->get('subject');
        $request = $subject->Request();

        // If this is a POST-Request, and affects our plugin, we may clear the config cache
        if ($request->isPost() && ('EnderecoShopware5Client' === $request->getParam('name'))) {
            $this->cacheManager->clearHttpCache();
            $this->cacheManager->clearConfigCache();
            $this->cacheManager->clearTemplateCache();
        }
    }

    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        $enderecoService = Shopware()->Container()->get('endereco_shopware5_client.endereco_service');

        $splitStreet = $this->config['splitStreet'];

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();
        $view->assign('endereco_split_street', $splitStreet);
        $view->assign('endereco_plugin_version', $enderecoService->getVersion());
        $view->assign('endereco_theme_name', $this->themeName);

        // Get country mapping.
        $countryRepository = Shopware()
            ->Container()
            ->get('models')
            ->getRepository(\Shopware\Models\Country\Country::class);
        $countries = $countryRepository->findBy(['active' => 1]);

        $countryMapping = [];
        $countryMappingId2Code = [];
        $countryMappingCode2Id = [];
        foreach ($countries as $country) {
            $countryMapping[$country->getIso()] = $country->getName();
            $countryMappingId2Code[$country->getId()] = $country->getIso();
            $countryMappingCode2Id[$country->getIso()] = $country->getId();
        }

        $view->assign('endereco_country_mapping', addslashes(json_encode($countryMapping)));
        $view->assign('endereco_country_id2code_mapping', addslashes(json_encode($countryMappingId2Code)));
        $view->assign('endereco_country_code2id_mapping', addslashes(json_encode($countryMappingCode2Id)));

        $subdivisionRepository = Shopware()
            ->Container()
            ->get('models')
            ->getRepository(\Shopware\Models\Country\State::class);
        $subdivisions = $subdivisionRepository->findBy(['active' => 1]);

        $subdivisionMapping = [];
        $subdivisionMappingId2Code = [];
        $subdivisionMappingCode2Id = [];
        foreach ($subdivisions as $subdivision) {
            $subdisivionCode = implode(
                '-',
                [
                    $countryMappingId2Code[$subdivision->getCountry()->getId()],
                    $subdivision->getShortCode()
                ]
            );
            $subdivisionMapping[$subdisivionCode] = $subdivision->getName();
            $subdivisionMappingId2Code[$subdivision->getId()] = $subdisivionCode;
            $subdivisionMappingCode2Id[$subdisivionCode] = $subdivision->getId();
        }

        $view->assign('endereco_subdivision_mapping', addslashes(json_encode($subdivisionMapping)));
        $view->assign('endereco_subdivision_id2code_mapping', addslashes(json_encode($subdivisionMappingId2Code)));
        $view->assign('endereco_subdivision_code2id_mapping', addslashes(json_encode($subdivisionMappingCode2Id)));

        // Create whitelist.
        // 1. These classes are always in the list.
        $whitelist = ['register', 'address', 'account', 'checkout', 'premsonepagecheckout'];
        $addController = explode(
            ',',
            strtolower(
                preg_replace('/\s+/', '', $this->config['whitelistController'])
            )
        );
        $whitelist = array_merge($whitelist, $addController);

        $view->assign('endereco_controller_whitelist', $whitelist);

        $view->assign('endereco_ams_is_active', $this->config['amsActive']);

        $view->assign('endereco_is_active', $this->config['isPluginActive']);

        $mainColorCode =  $this->config['mainColor'];
        if ($mainColorCode) {
            list($red, $gren, $blue) = $this->hex2rgb($mainColorCode);
            $mainColor = "rgb({$red}, {$gren}, {$blue})";
            $mainColorBG = "rgba({$red}, {$gren}, {$blue}, 0.1)";
            $view->assign('endereco_main_color', $mainColor);
            $view->assign('endereco_main_color_bg', $mainColorBG);
        }

        $errorColorCode = $this->config['errorColor'];
        if ($errorColorCode) {
            list($red, $gren, $blue) = $this->hex2rgb($errorColorCode);
            $errorColor = "rgb({$red}, {$gren}, {$blue})";
            $errorColorBG = "rgba({$red}, {$gren}, {$blue}, 0.125)";
            $view->assign('endereco_error_color', $errorColor);
            $view->assign('endereco_error_color_bg', $errorColorBG);
        }

        $successColorCode = $this->config['successColor'];
        if ($successColorCode) {
            list($red, $gren, $blue) = $this->hex2rgb($successColorCode);
            $successColor = "rgb({$red}, {$gren}, {$blue})";
            $successColorBG = "rgba({$red}, {$gren}, {$blue}, 0.125)";
            $view->assign('endereco_success_color', $successColor);
            $view->assign('endereco_success_color_bg', $successColorBG);
        }

        $view->assign('endereco_use_default_styles', $this->config['useDefaultCss']);
    }

    public function onCollectTemplateDir(\Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();
        $dirs[] = $this->pluginDir . '/Resources/views/';

        $args->setReturn($dirs);
    }

    private function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return array($r, $g, $b);
    }

    private function doAccounting()
    {
        $accountableSessionIds = array();

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            foreach ($_POST as $sVarName => $sVarValue) {
                if ((strpos($sVarName, '_session_counter') !== false) && 0 < intval($sVarValue)) {
                    $sSessionIdName = str_replace('_session_counter', '', $sVarName) . '_session_id';
                    $accountableSessionIds[$_POST[$sSessionIdName]] = true;
                }
            }

            $accountableSessionIds = array_keys($accountableSessionIds);
        }

        $this->enderecoService->sendDoAccountings($accountableSessionIds);
    }

    /**
     * Returns the most up-to-date list of payment names that indicate an existing customer.
     *
     * @return array List of payment method names
     */
    private function getExistingCustomerPaymentWhitelist()
    {
        $originalWhitelist = $this->defaultPaymentMethodsWhitelist;
        $additionalNames = explode(',', $this->config['whitelistPaymentMethod']);
        return array_merge($originalWhitelist, $additionalNames);
    }

    /**
     * Returns the most up-to-date list of payment names that indicate PayPal Express Checkout customer.
     *
     * @return array List of PayPal Express Checkout payment method names
     */
    private function getPayPalExpressCheckoutPaymentWhitelist()
    {
        $originalWhitelist = $this->paypalExpressCheckoutPaymentMethodsWhitelist;
        $additionalNames = explode(',', $this->config['alternativePayPalPaymentNames']);
        return array_merge($originalWhitelist, $additionalNames);
    }

    /**
     * Checks if the current payment method is in the given whitelist.
     *
     * @param string $currentPaymentMethod Name of the current payment method
     * @param array  $whitelist List of whitelisted payment method names
     *
     * @return bool True if the current payment method is in the whitelist, false otherwise
     */
    private function isCurrentPaymentMethodInWhitelist($currentPaymentMethod, array $whitelist)
    {
        $normalizedPaymentName = $this->normalizePaymentMethodName($currentPaymentMethod);
        $normalizedWhitelist = $this->normalizePaymentMethodNames($whitelist);
        return in_array($normalizedPaymentName, $normalizedWhitelist);
    }

    /**
     * Normalizes an array of payment method names. The normalization is used to make
     * comparison a bit more robust, as the user might potentially use lower and upper
     * case in unexpected manner.
     *
     * @param array $paymentMethodNames Array of payment method names to normalize
     *
     * @return array Normalized payment method names
     */
    private function normalizePaymentMethodNames(array $paymentMethodNames)
    {
        return array_map([$this, 'normalizePaymentMethodName'], $paymentMethodNames);
    }

    /**
     * Normalizes a single payment method name. This method reduces the variability in the way
     * a payment name can be written by a shop user, potentially making the comparison agains whitelist
     * more robust.
     *
     * @param string $paymentMethodName Payment method name to normalize
     *
     * @return string Normalized payment method name
     */
    private function normalizePaymentMethodName(string $paymentMethodName)
    {
        return mb_strtolower(
            trim(
                preg_replace('/\s+/', '', $paymentMethodName)
            )
        );
    }
}
