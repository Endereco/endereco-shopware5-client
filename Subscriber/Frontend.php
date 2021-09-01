<?php

namespace EnderecoShopware5Client\Subscriber;

use Enlight\Event\SubscriberInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\CacheManager;
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

    /**
     * @var CacheManager
     */
    private $cacheManager;

	/**
	 * @param string $pluginDir
	 */
	public function __construct($pluginDir, $pluginInfo, $logger, $cacheManager, $enderecoService) {
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
            $shop = Shopware()->Container()->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
        }

        $this->themeName = strtolower($shop->getTemplate()->getTemplate());
        $this->config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('EnderecoShopware5Client', $shop);
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

            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Account' => 'checkExistingCustomerAddresses',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'checkAdressesOrOpenModals',

            'Shopware_Modules_Order_SaveOrder_FilterAttributes' => 'onAfterOrderSaveOrder',
            'Shopware_Modules_Order_SaveOrder_FilterParams' => 'addCommentToOrder',
		];
	}

    public function sendDoAccountingForms($args) {
        $this->_doAccounting();
    }

    public function sendDoAccountingRegister($args) {
        $request = $args->getRequest();
        $blackListActions = [
            'ajax_validate_password',
            'ajax_validate_email'
        ];
        if (in_array($request->getActionName(), $blackListActions)) {
            return;
        }
        $this->_doAccounting();
    }

	public function sendDoAccountingAddress($args) {
	    $this->_doAccounting();
    }

	public function onAfterOrderSaveOrder($args) {
        $sOrder = $args->get('subject');
        $returnValue = $args->getReturn();

        if (!$this->config['isPluginActive']) {
            return $returnValue;
        }

        if ($sOrder->sUserData['billingaddress']['attributes']['enderecoamsstatus']) {
            $returnValue['endereco_order_billingamsstatus'] = $sOrder->sUserData['billingaddress']['attributes']['enderecoamsstatus'];
        }

        if ($sOrder->sUserData['shippingaddress']['attributes']['enderecoamsstatus']) {
            $returnValue['endereco_order_shippingamsstatus'] = $sOrder->sUserData['shippingaddress']['attributes']['enderecoamsstatus'];
        }

        if ($sOrder->sUserData['billingaddress']['attributes']['enderecoamsts']) {
            $returnValue['endereco_order_billingamsts'] = $sOrder->sUserData['billingaddress']['attributes']['enderecoamsts'];
        }

        if ($sOrder->sUserData['shippingaddress']['attributes']['enderecoamsts']) {
            $returnValue['endereco_order_shippingamsts'] = $sOrder->sUserData['shippingaddress']['attributes']['enderecoamsts'];
        }

        return $returnValue;
    }

    public function addCommentToOrder($args) {
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
            $predictions = json_decode($sOrder->sUserData['shippingaddress']['attributes']['enderecoamsapredictions'], true);
        } else {
            $predictions = [];
        }

        $curDate = date('d.m.Y H:i:s', time());

        // Write internal comment for specific case.
        // Case #1: Address was not found
        if (in_array('address_not_found', $statusCodes)) {
            $template = Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressTimestampCheckERP');
            $commentHeadline = sprintf($template, $curDate);
            $commentBody = Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressNotFoundMainERP');
            $returnValue['internalcomment'] = implode("\n", [$commentHeadline, $commentBody]);
            return $returnValue;
        }
        // Case #2: Address is correct -- dont save anything
        // Code removed

        // Case #3: Address needs correction
        if (in_array('address_needs_correction', $statusCodes)) {
            $template = Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressTimestampCheckERP');
            $commentHeadline = sprintf($template, $curDate);
            $commentBody = Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressNeedsCorrectionMainERP');
            if (!empty($predictions[0])) {
                $commentBody .= " ". Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressNeedsCorrectionSecondaryERP') ." \n";
                $commentCorrection = sprintf(
                    "  %s %s,  %s %s,  %s", // TODO: country specific formats.
                    $predictions[0]['streetName'],
                    $predictions[0]['buildingNumber'],
                    $predictions[0]['postalCode'],
                    $predictions[0]['locality'],
                    strtoupper($predictions[0]['countryCode'])
                );
            } else {
                $commentCorrection = null;
            }

            $returnValue['internalcomment'] = implode("\n", [$commentHeadline, $commentBody, $commentCorrection]);
            return $returnValue;
        }
        // Case #4: Address has multiple variants
        if (in_array('address_multiple_variants', $statusCodes)) {
            $template = Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressTimestampCheckERP');
            $commentHeadline = sprintf($template, $curDate);
            $commentBody = Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressMultipleVariantsMainERP');
            if (0 < count($predictions)) {
                $commentBody .= " " . Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('statusAddressMultipleVariantsSecondaryERP') . " \n";
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

	public function checkAdressesOrOpenModals($args) {
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

        /**
         * If existing customers can be checked and the payment method is whitelisted -> check it.
         */
        if ($this->config['checkExisting'] &&
            in_array($currentPaymentMethod, [
                'prepayment',
                'cash',
                'invoice',
                'debit',
                'sepa'
            ])
        ) {
            $continue = true;
        }

        /**
         * If paypal express check is active and payment method is paypalexpress -> check it.
         */
        if ($this->config['checkPayPalExpress'] &&
            in_array($currentPaymentMethod, [
                'SwagPaymentPayPalUnified',
            ])
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
                    (!array_key_exists('moptwunschpaketaddresstype', $address['attribute']) || !in_array($address['attribute']['moptwunschpaketaddresstype'], ['filiale', 'packstation'])) &&
                    (!$address['attribute']['enderecoamsstatus'] || (false !== strpos($address['attribute']['enderecoamsstatus'], 'address_not_checked')))
                ) {
                    $addressesToCheck[$address['id']] = true;
                }
            }

            // Check addresses.
            $checkedAddresses = $this->enderecoService->checkAddresses(array_keys($addressesToCheck));

            if ($addressesToCheck && (0 < $checkedAddresses) && !Shopware()->Session()->endereco_should_not_reload_anymore) {
                $view->assign('endereco_need_to_reload', true);
                Shopware()->Session()->endereco_should_not_reload_anymore = true;
                return;
            }
        }

        $needToCheckBilling = false;
        $needToCheckShipping = false;

        // Check if users billing address is alright.
        if (
            $sUserData &&
            $sUserData['billingaddress'] &&
            array_key_exists('enderecoamsstatus', $sUserData['billingaddress']['attributes']) &&
            !in_array('address_selected_by_customer', explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus'])) &&
            (
                in_array('address_needs_correction', explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus'])) ||
                in_array('address_multiple_variants', explode(',', $sUserData['billingaddress']['attributes']['enderecoamsstatus']))
            )

        ) {
            $needToCheckBilling = true;
        }
        // Check if users billing address is alright.
        if (
            $sUserData &&
            $sUserData['shippingaddress'] &&
            $sUserData['billingaddress']['id'] !== $sUserData['shippingaddress']['id'] &&
            array_key_exists('enderecoamsstatus', $sUserData['shippingaddress']['attributes']) &&
            !in_array('address_selected_by_customer', explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus'])) &&
            (
                in_array('address_needs_correction', explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus'])) ||
                in_array('address_multiple_variants', explode(',', $sUserData['shippingaddress']['attributes']['enderecoamsstatus']))
            )

        ) {
            $needToCheckShipping = true;
        }

        $view->assign('endereco_need_to_check_billing', $needToCheckBilling);
        $view->assign('endereco_need_to_check_shipping', $needToCheckShipping);
    }

	public function checkExistingCustomerAddresses($args) {
        $request = $args->getRequest();
        $controller = $args->get('subject');
        $view = $controller->View();
        $availableActions = ['login', 'index'];

        if (!in_array(strtolower($request->getActionName()), $availableActions, true)) {
            return;
        }

        if (!$this->config['checkExisting']) {
            return;
        }
        if (!$this->config['isPluginActive']) {
            return;
        }

        $sUserData = $view->getAssign('sUserData');

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
                    (!array_key_exists('moptwunschpaketaddresstype', $address['attribute']) || !in_array($address['attribute']['moptwunschpaketaddresstype'], ['filiale', 'packstation'])) &&
                    (!$address['attribute']['enderecoamsstatus'] || (false !== strpos($address['attribute']['enderecoamsstatus'], 'address_not_checked')))
                ) {
                    $addressesToCheck[$address['id']] = true;
                }
            }

            // Check addresses.
            $this->enderecoService->checkAddresses(array_keys($addressesToCheck));
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

	public function onPostDispatch(\Enlight_Event_EventArgs $args) {
        $enderecoService = Shopware()->Container()->get('endereco_shopware5_client.endereco_service');

        $splitStreet = $this->config['splitStreet'];

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();
        $view->assign('endereco_split_street', $splitStreet);
        $view->assign('endereco_plugin_version', $enderecoService->getVersion());
        $view->assign('endereco_theme_name', $this->themeName);

        // Get country mapping.
        $countryRepository = Shopware()->Container()->get('models')->getRepository(\Shopware\Models\Country\Country::class);
        $countries = $countryRepository->findBy(['active' => 1]);

        $countryMapping = [];
        foreach($countries as $country) {
            $countryMapping[$country->getIso()] = $country->getName();
        }

        $view->assign('endereco_country_mapping', addslashes(json_encode($countryMapping)));

        // Create whitelist.
        // 1. These classes are always in the list.
        $whitelist = ['register', 'address', 'account', 'checkout'];
        $addController = explode(
            ',',
            strtolower(
                preg_replace('/\s+/', '', $this->config['whitelistController'])
            )
        );
        if (!empty($addController)) {
            $whitelist = array_merge($whitelist, $addController);
        }
        $view->assign('endereco_controller_whitelist', $whitelist);

        $view->assign('endereco_is_active', $this->config['isPluginActive']);

        $mainColorCode =  $this->config['mainColor'];
        if ($mainColorCode) {
            list($red, $gren, $blue) = $this->_hex2rgb($mainColorCode);
            $mainColor = "rgb({$red}, {$gren}, {$blue})";
            $mainColorBG = "rgba({$red}, {$gren}, {$blue}, 0.1)";
            $view->assign('endereco_main_color', $mainColor);
            $view->assign('endereco_main_color_bg', $mainColorBG);
        }

        $errorColorCode = $this->config['errorColor'];
        if ($errorColorCode) {
            list($red, $gren, $blue) = $this->_hex2rgb($errorColorCode);
            $errorColor = "rgb({$red}, {$gren}, {$blue})";
            $errorColorBG = "rgba({$red}, {$gren}, {$blue}, 0.125)";
            $view->assign('endereco_error_color', $errorColor);
            $view->assign('endereco_error_color_bg', $errorColorBG);
        }

        $successColorCode = $this->config['successColor'];
        if ($successColorCode) {
            list($red, $gren, $blue) = $this->_hex2rgb($successColorCode);
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

    private function _hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        return array($r, $g, $b);
    }

    private function _doAccounting() {
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
}
