<?php

namespace EnderecoShopware5Client;

use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
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
        $temp = explode('\\', get_class($this));
        $className =  $temp[count($temp)-1];
        $isStoreVersion = ('Endereco' . 'AMS') === $className;
        $pluginManager = $this->container->get('shopware_plugininstaller.plugin_manager');
        if ($isStoreVersion) {
            // Try to deactivate open source version.
            try {

                $plugin = $pluginManager->getPluginByName('EnderecoShopware5' . 'Client');
                // Is it active?
                if ($plugin->getInstalled()) {
                    $pluginManager->deactivatePlugin($plugin);
                }
            } catch(\Exception $e) {
                // Not installed.
            }
        } else {
            // Try to deactivate store version.
            try {

                $plugin = $pluginManager->getPluginByName('Endereco' . 'AMS');
                // Is it active?
                if ($plugin->getInstalled()) {
                    $pluginManager->deactivatePlugin($plugin);
                }
            } catch(\Exception $e) {
                // Not installed.
            }
        }

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

        // Attribute sin the order.
        if ($service->get('s_order_attributes', 'endereco_order_billingamsts')) {
            $service->delete('s_order_attributes', 'endereco_order_billingamsts');
        }
        if ($service->get('s_order_attributes', 'endereco_order_shippingamsts')) {
            $service->delete('s_order_attributes', 'endereco_order_shippingamsts');
        }
        if ($service->get('s_order_attributes', 'endereco_order_billingamsstatus')) {
            $service->delete('s_order_attributes', 'endereco_order_billingamsstatus');
        }
        if ($service->get('s_order_attributes', 'endereco_order_shippingamsstatus')) {
            $service->delete('s_order_attributes', 'endereco_order_shippingamsstatus');
        }

        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);
        $uninstallContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
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
        if (!$service->get('s_order_attributes', 'endereco_order_billingamsts')) {
            $service->update('s_order_attributes', 'endereco_order_billingamsts', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_attributes', 'endereco_order_shippingamsts')) {
            $service->update('s_order_attributes', 'endereco_order_shippingamsts', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_attributes', 'endereco_order_billingamsstatus')) {
            $service->update('s_order_attributes', 'endereco_order_billingamsstatus', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_attributes', 'endereco_order_shippingamsstatus')) {
            $service->update('s_order_attributes', 'endereco_order_shippingamsstatus', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'displayInBackend' => false,
                'custom' => true
            ]);
        }

        // If current plugin is EnderecoAMS, the GitHub version is not installed, try to remove old attributes.
        $temp = explode('\\', get_class($this));
        $className =  $temp[count($temp)-1];
        $isStoreVersion = ('Endereco' . 'AMS') === $className;
        $openSourceVersionInstalled = false;

        try {
            $pluginManager = $this->container->get('shopware_plugininstaller.plugin_manager');
            $plugin = $pluginManager->getPluginByName('EnderecoShopware5' . 'Client');
            // Is it active?
            if ($plugin->getInstalled()) {
                $openSourceVersionInstalled = true;
            }
        } catch(\Exception $e) {
            // Not installed.
        }

        if (
            $isStoreVersion && !$openSourceVersionInstalled
        ) {
            if ($service->get('s_user_addresses_attributes', 'endereco'.'amsts')) {
                $service->delete('s_user_addresses_attributes', 'endereco'.'amsts');
            }
            if ($service->get('s_user_addresses_attributes', 'enderecoams'.'status')) {
                $service->delete('s_user_addresses_attributes', 'enderecoams'.'status');
            }
            if ($service->get('s_user_addresses_attributes', 'enderecoamsa'.'predictions')) {
                $service->delete('s_user_addresses_attributes', 'enderecoamsa'.'predictions');
            }
        }

        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        $metaDataCache->deleteAll();
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes']);
    }
}
