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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
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

        // Standard address table attributes.
        if ($service->get('s_user_addresses_attributes', 'enderecoamsts')) {
            $service->delete('s_user_addresses_attributes', 'enderecoamsts');
        }
        if ($service->get('s_user_addresses_attributes', 'enderecoamsstatus')) {
            $service->delete('s_user_addresses_attributes', 'enderecoamsstatus');
        }
        if ($service->get('s_user_addresses_attributes', 'enderecoamsapredictions')) {
            $service->delete('s_user_addresses_attributes', 'enderecoamsapredictions');
        }
        if ($service->get('s_user_addresses_attributes', 'enderecostreetname')) {
            $service->delete('s_user_addresses_attributes', 'enderecostreetname');
        }
        if ($service->get('s_user_addresses_attributes', 'enderecobuildingnumber')) {
            $service->delete('s_user_addresses_attributes', 'enderecobuildingnumber');
        }

        // Order billing address table attributes.
        if ($service->get('s_order_billingaddress_attributes', 'enderecoamsts')) {
            $service->delete('s_order_billingaddress_attributes', 'enderecoamsts');
        }
        if ($service->get('s_order_billingaddress_attributes', 'enderecoamsstatus')) {
            $service->delete('s_order_billingaddress_attributes', 'enderecoamsstatus');
        }
        if ($service->get('s_order_billingaddress_attributes', 'enderecoamsapredictions')) {
            $service->delete('s_order_billingaddress_attributes', 'enderecoamsapredictions');
        }
        if ($service->get('s_order_billingaddress_attributes', 'enderecostreetname')) {
            $service->delete('s_order_billingaddress_attributes', 'enderecostreetname');
        }
        if ($service->get('s_order_billingaddress_attributes', 'enderecobuildingnumber')) {
            $service->delete('s_order_billingaddress_attributes', 'enderecobuildingnumber');
        }

        // Order shipping address table attributes.
        if ($service->get('s_order_shippingaddress_attributes', 'enderecoamsts')) {
            $service->delete('s_order_shippingaddress_attributes', 'enderecoamsts');
        }
        if ($service->get('s_order_shippingaddress_attributes', 'enderecoamsstatus')) {
            $service->delete('s_order_shippingaddress_attributes', 'enderecoamsstatus');
        }
        if ($service->get('s_order_shippingaddress_attributes', 'enderecoamsapredictions')) {
            $service->delete('s_order_shippingaddress_attributes', 'enderecoamsapredictions');
        }
        if ($service->get('s_order_shippingaddress_attributes', 'enderecostreetname')) {
            $service->delete('s_order_shippingaddress_attributes', 'enderecostreetname');
        }
        if ($service->get('s_order_shippingaddress_attributes', 'enderecobuildingnumber')) {
            $service->delete('s_order_shippingaddress_attributes', 'enderecobuildingnumber');
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

        // Meta-Data attributes
        $metaAttributes = [
            'endereco_status',
            'endereco_predictions',
            'endereco_hash'
        ];

        foreach ($metaAttributes as $attribute) {
            if ($service->get('s_user_addresses_attributes', $attribute)) {
                $service->delete('s_user_addresses_attributes', $attribute);
            }
        }

        // Session-Data attributes
        $sessionAttributes = [
            'endereco_session_id',
            'endereco_session_counter'
        ];

        foreach ($sessionAttributes as $attribute) {
            if ($service->get('s_user_addresses_attributes', $attribute)) {
                $service->delete('s_user_addresses_attributes', $attribute);
            }
        }

        // Ensure attributes are removed from order addresses as well
        $orderAttributes = array_merge($metaAttributes, $sessionAttributes);
        foreach ($orderAttributes as $attribute) {
            if ($service->get('s_order_billingaddress_attributes', $attribute)) {
                $service->delete('s_order_billingaddress_attributes', $attribute);
            }
            if ($service->get('s_order_shippingaddress_attributes', $attribute)) {
                $service->delete('s_order_shippingaddress_attributes', $attribute);
            }
        }

        // Ensure attributes are removed from user as well
        $orderAttributes = array_merge($metaAttributes, $sessionAttributes);
        foreach ($orderAttributes as $attribute) {
            if ($service->get('s_user_attributes', $attribute)) {
                $service->delete('s_user_attributes', $attribute);
            }
            if ($service->get('s_user_attributes', $attribute)) {
                $service->delete('s_user_attributes', $attribute);
            }
        }

        $metaDataCache = Shopware()->Models()->getConfiguration()->getMetadataCacheImpl();
        if (method_exists($metaDataCache, 'deleteAll')) {
            $metaDataCache->deleteAll();
        }
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes', 's_order_attributes', 's_order_billingaddress_attributes', 's_order_shippingaddress_attributes']);
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

        // Default address attributes.
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
                'label' => 'JSON mit möglicher Adresskorrekturen',
                'displayInBackend' => false,
                'custom' => true
            ]);
        }
        if (!$service->get('s_user_addresses_attributes', 'enderecostreetname')) {
            $service->update('s_user_addresses_attributes', 'enderecostreetname', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Straßenname',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }
        if (!$service->get('s_user_addresses_attributes', 'enderecobuildingnumber')) {
            $service->update('s_user_addresses_attributes', 'enderecobuildingnumber', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Hausnummer',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }

        // Billing  address attributes.
        if (!$service->get('s_order_billingaddress_attributes', 'enderecoamsts')) {
            $service->update('s_order_billingaddress_attributes', 'enderecoamsts', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Zeitpunkt der Adressprüfung',
                'custom' => true,
                'displayInBackend' => true
            ]);
        }
        if (!$service->get('s_order_billingaddress_attributes', 'enderecoamsstatus')) {
            $service->update('s_order_billingaddress_attributes', 'enderecoamsstatus', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Status der Adressprüfung',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_billingaddress_attributes', 'enderecoamsapredictions')) {
            $service->update('s_order_billingaddress_attributes', 'enderecoamsapredictions', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'JSON mit möglicher Adresskorrekturen',
                'displayInBackend' => false,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_billingaddress_attributes', 'enderecostreetname')) {
            $service->update('s_order_billingaddress_attributes', 'enderecostreetname', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Straßenname',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_billingaddress_attributes', 'enderecobuildingnumber')) {
            $service->update('s_order_billingaddress_attributes', 'enderecobuildingnumber', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Hausnummer',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }

        // Shipping address attributes.
        if (!$service->get('s_order_shippingaddress_attributes', 'enderecoamsts')) {
            $service->update('s_order_shippingaddress_attributes', 'enderecoamsts', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Zeitpunkt der Adressprüfung',
                'custom' => true,
                'displayInBackend' => true
            ]);
        }
        if (!$service->get('s_order_shippingaddress_attributes', 'enderecoamsstatus')) {
            $service->update('s_order_shippingaddress_attributes', 'enderecoamsstatus', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Status der Adressprüfung',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_shippingaddress_attributes', 'enderecoamsapredictions')) {
            $service->update('s_order_shippingaddress_attributes', 'enderecoamsapredictions', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'JSON mit möglicher Adresskorrekturen',
                'displayInBackend' => false,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_shippingaddress_attributes', 'enderecostreetname')) {
            $service->update('s_order_shippingaddress_attributes', 'enderecostreetname', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Straßenname',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }
        if (!$service->get('s_order_shippingaddress_attributes', 'enderecobuildingnumber')) {
            $service->update('s_order_shippingaddress_attributes', 'enderecobuildingnumber', \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                'label' => 'Hausnummer',
                'displayInBackend' => true,
                'custom' => true
            ]);
        }

        // Order attributes.
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

        // Meta-Data attributes
        $metaAttributes = [
            'endereco_status' => 'Status der Adressprüfung',
            'endereco_predictions' => 'JSON mit möglicher Adresskorrekturen',
            'endereco_hash' => 'Hash der Daten'
        ];

        foreach ($metaAttributes as $attribute => $label) {
            if (!$service->get('s_user_addresses_attributes', $attribute)) {
                $service->update('s_user_addresses_attributes', $attribute, \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                    'label' => $label,
                    'displayInBackend' => true,
                    'custom' => true
                ]);
            }
        }

        // Session-Data attributes
        $sessionAttributes = [
            'endereco_session_id' => 'Session ID',
            'endereco_session_counter' => 'Session Counter'
        ];

        foreach ($sessionAttributes as $attribute => $label) {
            if (!$service->get('s_user_addresses_attributes', $attribute)) {
                $service->update('s_user_addresses_attributes', $attribute, \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                    'label' => $label,
                    'displayInBackend' => true,
                    'custom' => true
                ]);
            }
        }

        // Ensure attributes are added to order addresses as well
        $orderAttributes = array_merge($metaAttributes, $sessionAttributes);
        foreach ($orderAttributes as $attribute => $label) {
            if (!$service->get('s_order_billingaddress_attributes', $attribute)) {
                $service->update('s_order_billingaddress_attributes', $attribute, \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                    'label' => $label,
                    'displayInBackend' => true,
                    'custom' => true
                ]);
            }
            if (!$service->get('s_order_shippingaddress_attributes', $attribute)) {
                $service->update('s_order_shippingaddress_attributes', $attribute, \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                    'label' => $label,
                    'displayInBackend' => true,
                    'custom' => true
                ]);
            }
        }

        // Ensure attributes are added to user as well
        $userAttributes = array_merge($metaAttributes, $sessionAttributes);
        foreach ($userAttributes as $attribute => $label) {
            if (!$service->get('s_user_attributes', $attribute)) {
                $service->update('s_user_attributes', $attribute, \Shopware\Bundle\AttributeBundle\Service\TypeMapping::TYPE_STRING, [
                    'label' => $label,
                    'displayInBackend' => true,
                    'custom' => true
                ]);
            }
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
        if (method_exists($metaDataCache, 'deleteAll')) {
            $metaDataCache->deleteAll();
        }
        Shopware()->Models()->generateAttributeModels(['s_user_addresses_attributes', 's_order_attributes', 's_order_billingaddress_attributes', 's_order_shippingaddress_attributes', 's_user_attributes']);
    }
}
