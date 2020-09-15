<?php

namespace EnderecoShopware5Client\Subscriber;

use Enlight\Event\SubscriberInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Shopware\Components\Plugin\XmlReader\XmlPluginReader;
use Shopware\Components\CacheManager;
use Shopware\Components\Theme\LessDefinition;
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

    /**
     * @var CacheManager
     */
    private $cacheManager;

	/**
	 * @param string $pluginDir
	 */
	public function __construct($pluginDir, $pluginInfo, $logger, $cacheManager) {
		$this->pluginDir = $pluginDir;
		$this->pluginInfo = $pluginInfo;
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
        $this->http = new \GuzzleHttp\Client(['timeout' => 3.0, 'connection_timeout' => 2.0]);;
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
            'Shopware\Models\Customer\Address::preRemove' => 'onPreRemove',

            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatchSecure_Backend_Config' => 'onPostDispatchConfig'
		];
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
            $this->cacheManager->clearByTag(CacheManager::CACHE_TAG_CONFIG);
            $this->cacheManager->clearByTag(CacheManager::CACHE_TAG_TEMPLATE);
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
        $config = Shopware()->Container()->get('config');
        $anyDoAccounting = false;
        $info = 'Endereco Shopware5 Client v'.$this->pluginInfo['version'];
        $apikey = $config->get('apiKey');
        $endpoint = $config->get('remoteApiUrl');

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            foreach ($_POST as $sVarName => $sVarValue) {

                if ((strpos($sVarName, '_session_counter') !== false) && 0 < intval($sVarValue)) {
                    $sSessionIdName = str_replace('_session_counter', '', $sVarName) . '_session_id';
                    $sSessionId = $_POST[$sSessionIdName];
                    try {
                        $message = array(
                            'jsonrpc' => '2.0',
                            'id' => 1,
                            'method' => 'doAccounting',
                            'params' => array(
                                'sessionId' => $sSessionId
                            )
                        );
                        $newHeaders = array(
                            'Content-Type' => 'application/json',
                            'X-Auth-Key' => $apikey,
                            'X-Transaction-Id' => $sSessionId,
                            'X-Transaction-Referer' => 'EnderecoShopware5Client\Subscriber\Frontend.php',
                            'X-Agent' => $info,
                        );
                        $this->http->post(
                            $endpoint,
                            array(
                                'headers' => $newHeaders,
                                'body' => json_encode($message)
                            )
                        );
                        $anyDoAccounting = true;

                    } catch(\Exception $e) {
                        $this->logger->addError($e->getMessage());
                    }
                }
            }

        }

        if ($anyDoAccounting) {
            try {
                $message = array(
                    'jsonrpc' => '2.0',
                    'id' => 1,
                    'method' => 'doConversion',
                    'params' => array()
                );
                $newHeaders = array(
                    'Content-Type' => 'application/json',
                    'X-Auth-Key' => $apikey,
                    'X-Transaction-Id' => 'not_required',
                    'X-Transaction-Referer' => 'EnderecoShopware5Client\Subscriber\Frontend.php',
                    'X-Agent' => $info,
                );
                $this->http->post(
                    $endpoint,
                    array(
                        'headers' => $newHeaders,
                        'body' => json_encode($message)
                    )
                );
            } catch(\Exception $e) {
                $this->logger->addError($e->getMessage());
            }
        }
    }
}
