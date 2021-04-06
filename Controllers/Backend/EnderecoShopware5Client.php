<?php

use Shopware\Components\Logger;
use Shopware\Components\CSRFWhitelistAware;
use Symfony\Component\HttpFoundation\Response;

class Shopware_Controllers_Backend_EnderecoShopware5Client extends \Shopware_Controllers_Backend_ExtJs implements CSRFWhitelistAware
{
    /**
     * @var Logger
     */
    private $logger;
    private $http;

	public function indexAction()
    {
	}

	public function testApiAction() {
        $this->logger = Shopware()->Container()->get('pluginlogger');
        $this->http = Shopware()->Container()->get('http_client');

        $readinessCheckRequest = array(
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'readinessCheck',
        );
        $dataString = json_encode($readinessCheckRequest);

        //$config = Shopware()->Container()->get('config');
        $shop = false;
        if (Shopware()->Container()->initialized('shop')) {
            $shop = Shopware()->Container()->get('shop');
        }
        if (!$shop) {
            $shop = Shopware()->Container()->get('models')->getRepository(\Shopware\Models\Shop\Shop::class)->getActiveDefault();
        }
        $config = Shopware()->Container()->get('shopware.plugin.cached_config_reader')->getByPluginName('EnderecoShopware5Client', $shop);

        if ($this->request->getParam('apiKey')) {
            $apiKey = $this->request->getParam('apiKey');
        } else {
            $apiKey = $config['apiKey'];
        }

        if (!$apiKey) {
            $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $this->logger->addError(Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('apiError'));
            $this->View()->assign('response', Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('apiError'));
            return;
        }

        $xml = simplexml_load_file(dirname(dirname(dirname(__FILE__))) . '/plugin.xml');
        $agent_info  = "Endereco Shopware5 Client (Download) v" . $xml->version;

        try {
            $response = $this->http->post(
                $config['remoteApiUrl'],
                array(
                    'Content-Type' => 'application/json',
                    'X-Auth-Key' => $apiKey,
                    'X-Transaction-Id' => 'not_required',
                    'X-Transaction-Referer' => $_SERVER['HTTP_REFERER'],
                    'X-Agent' => $agent_info,
                ),
                $dataString
            );

            $status = json_decode($response->getBody(), true);
            if ('ready' === $status['result']['status']) {
                $this->View()->assign('response', Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('apiOK'));
            } else {
                $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $this->View()->assign('response', Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('apiError'));
            }
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            // Log it.
            $this->logger->addError($exception->getMessage());

            $this->response->setStatusCode(Response::HTTP_BAD_REQUEST);

            if (strpos($errorMessage, '400') !== false) {
                $this->View()->assign('response', Shopware()->Snippets()->getNamespace('EnderecoShopware5Client')->get('apiError'));
            } else {
                $this->View()->assign('response', $exception->getMessage());
            }
        }
	}

	public function getWhitelistedCSRFActions()
	{
		return [
			'testApi',
		];
	}
}
