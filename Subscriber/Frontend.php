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
	}

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			'Theme_Compiler_Collect_Plugin_Javascript' => 'onCollectJavascript',
			'Theme_Inheritance_Template_Directories_Collected' => 'onCollectTemplateDir',

            'Shopware\Models\Customer\Address::postPersist' => 'onPostPersist',
            'Shopware\Models\Customer\Address::postUpdate' => 'onPostUpdate',

            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Config' => 'onPostDispatchConfig',

            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Account' => 'checkExistingCustomerAddresses',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'checkAdressesOrOpenModals',
		];
	}

	public function checkAdressesOrOpenModals($args) {
        $request = $args->getRequest();
        $controller = $args->get('subject');
        $view = $controller->View();
        $availableActions = ['confirm'];

        if (!in_array(strtolower($request->getActionName()), $availableActions, true)) {
            return;
        }

        $config = Shopware()->Container()->get('config');
        if (!$config->get('checkExisting')) {
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

        $config = Shopware()->Container()->get('config');
        if (!$config->get('checkExisting')) {
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

	public function onPostPersist($args) {
        $this->_doAccounting();
    }

    public function onPostUpdate($args) {
        $this->_doAccounting();
    }

    public function onPreRemove($args) {
        $this->_doAccounting();
    }

	public function onCollectJavascript()
	{
		$jsPath = [
			$this->pluginDir . '/Resources/views/frontend/_public/src/js/shopware5-bundle.js',
		];

		return new ArrayCollection($jsPath);
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
        $config = Shopware()->Container()->get('config');
        $splitStreet = $config->get('splitStreet');

        /** @var \Enlight_Controller_Action $controller */
        $controller = $args->get('subject');
        $view = $controller->View();
        $view->assign('endereco_split_street', $splitStreet);

        $mainColorCode = $config->get('mainColor');
        if ($mainColorCode) {
            list($red, $gren, $blue) = $this->_hex2rgb($mainColorCode);
            $mainColor = "rgb({$red}, {$gren}, {$blue})";
            $mainColorBG = "rgba({$red}, {$gren}, {$blue}, 0.1)";
            $view->assign('endereco_main_color', $mainColor);
            $view->assign('endereco_main_color_bg', $mainColorBG);
        }


        $errorColorCode = $config->get('errorColor');
        if ($errorColorCode) {
            list($red, $gren, $blue) = $this->_hex2rgb($errorColorCode);
            $errorColor = "rgb({$red}, {$gren}, {$blue})";
            $errorColorBG = "rgba({$red}, {$gren}, {$blue}, 0.125)";
            $view->assign('endereco_error_color', $errorColor);
            $view->assign('endereco_error_color_bg', $errorColorBG);
        }

        $successColorCode = $config->get('successColor');
        if ($successColorCode) {
            list($red, $gren, $blue) = $this->_hex2rgb($successColorCode);
            $successColor = "rgb({$red}, {$gren}, {$blue})";
            $successColorBG = "rgba({$red}, {$gren}, {$blue}, 0.125)";
            $view->assign('endereco_success_color', $successColor);
            $view->assign('endereco_success_color_bg', $successColorBG);
        }

        $view->assign('endereco_use_default_styles', $config->get('useDefaultCss'));
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
