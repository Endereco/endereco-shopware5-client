<?php

namespace EnderecoShopware5Client;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use EnderecoShopware5Client\Strategies\AddMetaAndSessionAttributes;
use EnderecoShopware5Client\Strategies\AddLegacyStreetAttributes;
use EnderecoShopware5Client\Strategies\RemoveLegacyMetaAttributes;
use EnderecoShopware5Client\Strategies\RemoveLegacyStreetAttributes;
use EnderecoShopware5Client\Strategies\RemoveMetaAndSessionAttributes;
use EnderecoShopware5Client\Strategies\DeactivateOtherPluginVersion;

class EnderecoShopware5Client extends Plugin
{
    /**
     * Builds the plugin by setting container parameters and loading plugin information.
     *
     * @param ContainerBuilder $container The container builder instance.
     * 
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('endereco_shopware5_client.plugin_dir', $this->getPath());
        $container->setParameter('endereco_shopware5_client.plugin_info', $this->loadPluginXml());

        parent::build($container);
    }

    /**
     * Loads plugin information from the plugin.xml file.
     *
     * @return array The plugin information as an associative array.
     */
    public function loadPluginXml()
    {
        $pluginInfoPath = $this->getPath() . '/plugin.xml';
        if (is_file($pluginInfoPath)) {
            $info = json_decode(json_encode(simplexml_load_file($pluginInfoPath)), true);
        } else {
            $info = [];
        }

        return $info;
    }

    /**
     * Gets the technical name of the plugin.
     *
     * @return string The technical name of the plugin.
     */
    public function getTechnicalName()
    {
        $temp = explode('\\', get_class($this));
        $className =  $temp[count($temp) - 1];
        return $className;
    }

    /**
     * Adds necessary attributes when installing the plugin.
     *
     * @param InstallContext $installContext The install context instance.
     * 
     * @return void
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(InstallContext $installContext)
    {
        /**
         * @var CrudService $crud
         */
        $crud = $this->container->get('shopware_attribute.crud_service');

        (new AddMetaAndSessionAttributes())->execute($crud);
        (new AddLegacyStreetAttributes())->execute($crud);
    }

    /**
     * Deactivates other version (if its active) and clears cache when plugin is
     * activated.
     *
     * @param ActivateContext $activateContext The activate context instance.
     * 
     * @return void
     */
    public function activate(ActivateContext $activateContext)
    {
        /**
         * @var InstallerService $pluginManager
         */
        $pluginManager = $this->container->get('shopware_plugininstaller.plugin_manager');

        (new DeactivateOtherPluginVersion())->execute(
            $pluginManager,
            $this->getTechnicalName()
        );

        $activateContext->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    /**
     * Deactivates the plugin and schedules cache clearing.
     *
     * @param DeactivateContext $deactivateContext The deactivate context instance.
     * 
     * @return void
     */
    public function deactivate(DeactivateContext $deactivateContext)
    {
        $deactivateContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    /**
     * Uninstalls the plugin, removes attributes and schedules cache clearing.
     *
     * @param UninstallContext $uninstallContext The uninstall context instance.
     * 
     * @return void
     */
    public function uninstall(UninstallContext $uninstallContext)
    {
        /**
         * @var CrudService $crud
         */
        $crud = $this->container->get('shopware_attribute.crud_service');

        (new RemoveMetaAndSessionAttributes())->execute($crud);
        (new RemoveLegacyStreetAttributes())->execute($crud);
        (new RemoveLegacyMetaAttributes())->execute($crud);

        $uninstallContext->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    /**
     * Updates the plugin by removing old attributes, adding new ones, and clearing cache.
     *
     * @param UpdateContext $updateContext The update context instance.
     * 
     * @return void
     */
    public function update(UpdateContext $updateContext)
    {
        /**
         * @var CrudService $crud
         */
        $crud = $this->container->get('shopware_attribute.crud_service');

        (new RemoveLegacyMetaAttributes())->execute($crud);
        (new AddMetaAndSessionAttributes())->execute($crud);
        (new AddLegacyStreetAttributes())->execute($crud);
        
        $updateContext->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }
}
