<?php

namespace EnderecoShopware5Client\Strategies;

use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;

class DeactivateOtherPluginVersion
{
    /**
     * @var array A mapping of plugin technical names to their corresponding alternate plugin names.
     */
    private $otherPluginMapping = [
        'Endereco' . 'AMS' => 'EnderecoShopware5' . 'Client',
        'EnderecoShopware5' . 'Client' => 'Endereco' . 'AMS'
    ];

    /**
     * Deactivates the alternate version of the plugin if it is installed.
     *
     * @param InstallerService $pluginManager The plugin manager service used to manage plugins.
     * @param string $technicalPluginName The technical name of the plugin that is currently active.
     *
     * @return void
     */
    public function execute(InstallerService $pluginManager, $technicalPluginName)
    {
        $otherPluginName = $this->otherPluginMapping[$technicalPluginName];
        try {
            $plugin = $pluginManager->getPluginByName($otherPluginName);
            if ($plugin->getInstalled()) {
                $pluginManager->deactivatePlugin($plugin);
            }
        } catch (\Exception $e) {
            // TODO: add logging
        }
    }
}
