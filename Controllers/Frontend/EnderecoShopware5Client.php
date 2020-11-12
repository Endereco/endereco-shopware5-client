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
            $temp = $countryRepository->find($countryId);
            if ($temp) {
                $return = strtolower($temp->getIso());
            }
        } elseif (isset($countryCode) && '' !== $countryCode) {
            $temp = $countryRepository->findOneBy(array('iso' => $countryCode));
            if ($temp) {
                $return = strtolower($temp->getId());
            }
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
