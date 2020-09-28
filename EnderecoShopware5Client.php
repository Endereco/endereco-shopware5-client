<?php

namespace EnderecoShopware5Client;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
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
            $info = json_decode(json_encode(simplexml_load_file($pluginInfoPath)), true);
        } else {
            $info = [];
        }

        $container->setParameter('endereco_shopware5_client.plugin_info', $info);
        parent::build($container);
    }

    public function install(InstallContext $installContext)
    {
        $this->_addAttributes();
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
        if ($service->get('s_user_addresses_attributes', 'enderecoamsts')) {
            $service->delete('s_user_addresses_attributes', 'enderecoamsts');
        }
        if ($service->get('s_user_addresses_attributes', 'enderecoamsstatus')) {
            $service->delete('s_user_addresses_attributes', 'enderecoamsstatus');
        }
        if ($service->get('s_user_addresses_attributes', 'enderecoamsapredictions')) {
            $service->delete('s_user_addresses_attributes', 'enderecoamsapredictions');
        }
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);
    }

    public function update(UpdateContext $updateContext)
    {
        $this->_addAttributes();
        $updateContext->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }

    private function _addAttributes()
    {
        /**
         * @var \Shopware\Bundle\AttributeBundle\Service\CrudService
         */
        $service = $this->container->get('shopware_attribute.crud_service');
        if (!$service->get('s_user_addresses_attributes', 'enderecoamsts')) {
            $service->update('s_user_addresses_attributes', 'enderecoamsts', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Zeitpunkt der Adressprüfung',
                'custom' => true,
                'displayInBackend' => true
            ]);
        }
        if (!$service->get('s_user_addresses_attributes', 'enderecoamsstatus')) {
            $service->update('s_user_addresses_attributes', 'enderecoamsstatus', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Status der Adressprüfung',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }
        if (!$service->get('s_user_addresses_attributes', 'enderecoamsapredictions')) {
            $service->update('s_user_addresses_attributes', 'enderecoamsapredictions', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'custom' => true
            ]);
        }
        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);
    }
}
