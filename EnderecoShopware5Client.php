<?php
namespace EnderecoShopware5Client;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\XmlReader\XmlPluginReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EnderecoShopware5Client extends Plugin
{

	/**
	 * {@inheritdoc}
	 */
	public function build(ContainerBuilder $container)
	{
		$container->setParameter('endereco_shopware5_client.plugin_dir', $this->getPath());

        $pluginInfoPath = $this->getPath() . '/plugin.xml';
        if (is_file($pluginInfoPath)) {
            $xmlConfigReader = new XmlPluginReader();
            $info = $xmlConfigReader->read($pluginInfoPath);
        } else {
            $info = [];
        }

        $container->setParameter('endereco_shopware5_client.plugin_info', $info);
		parent::build($container);
	}

    public function install(InstallContext $installContext)
    {
        /**
         * @var \Shopware\Bundle\AttributeBundle\Service\CrudService
         */
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->update('s_user_addresses_attributes','enderecoamsts', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
            'label' => 'Zeitpunkt der Adressprüfung',
            'custom' => true,
            'displayInBackend' => true
        ]);
        $service->update('s_user_addresses_attributes', 'enderecoamsstatus', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
            'label' => 'Status der Adressprüfung',
            'displayInBackend' => true,
            'custom' => true
        ]);
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);

        // Set default value.

        $installContext->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

	public function activate(ActivateContext $activateContext)
	{
		$activateContext->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
	}

	public function deactivate(DeactivateContext $deactivateContext)
	{
		$deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
	}

	public function uninstall(UninstallContext $uninstallContext)
	{
        $service = $this->container->get('shopware_attribute.crud_service');
        $service->delete('s_user_addresses_attributes','enderecoamsts');
        $service->delete('s_user_addresses_attributes', 'enderecoamsstatus');
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);
		$uninstallContext->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
	}

    public function update(UpdateContext $updateContext)
    {
        $updateContext->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }
}
