<?php
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_EnderecoShopware5Client extends \Enlight_Controller_Action implements CSRFWhitelistAware
{
	public function indexAction()
    {
	}

	public function countryAction() {
	    $return = '';
	    $countryId = $this->request->getParam('countryId');
        $countryCode = $this->request->getParam('countryCode');
        $countryRepository = $this->container->get('models')->getRepository(\Shopware\Models\Country\Country::class);

        if (isset($countryId) && '' !== $countryId) {
            $return = strtolower($countryRepository->find($countryId)->getIso());
        } elseif (isset($countryCode) && '' !== $countryCode) {
            $return = strtolower($countryRepository->findOneBy(array('iso' => $countryCode))->getId());
        }

        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
        $response = $this->Response();
        $response->setBody($return);
	}

	public function getWhitelistedCSRFActions()
	{
		return [
			'index',
			'country',
		];
	}
}
