<?php

namespace FinSearchUnified;

use Exception;
use FinSearchUnified\Bundle\ControllerBundle\DependencyInjection\Compiler\RegisterControllerCompilerPass;
use Shopware\Bundle\PluginInstallerBundle\Service\InstallerService;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\FormSynchronizer;
use Shopware\Components\Plugin\XmlReader\XmlConfigReader;
use Shopware\Models;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class FinSearchUnified extends Plugin
{
    public function build(ContainerBuilder $container)
    {
        // Required for Shopware 5.2.x compatibility.
        if (!$container->hasParameter($this->getContainerPrefix() . '.plugin_dir')) {
            $container->setParameter($this->getContainerPrefix() . '.plugin_dir', $this->getPath());
        }
        if (!$container->hasParameter($this->getContainerPrefix() . '.plugin_name')) {
            $container->setParameter($this->getContainerPrefix() . '.plugin_name', $this->getName());
        }
        if (!$container->has('shopware_search_es.product_number_search_factory')) {
            $loader = new XmlFileLoader(
                $container,
                new FileLocator()
            );

            $loader->load($this->getPath() . '/Resources/shopware/searchBundleES.xml');
        }

        $container->addCompilerPass(new RegisterControllerCompilerPass([$this]));
        parent::build($container);
    }

    public function deactivate(DeactivateContext $context)
    {
        $this->deactivateCustomizedPlugin();
        parent::deactivate($context);
    }

    public function uninstall(UninstallContext $context)
    {
        $this->deactivateCustomizedPlugin();
        $context->scheduleClearCache([UninstallContext::CACHE_TAG_THEME]);
        parent::uninstall($context);
    }

    public function install(InstallContext $context)
    {
        $context->scheduleClearCache([InstallContext::CACHE_TAG_THEME]);
        parent::install($context);

        $shopwareVersion = Shopware()->Config()->get('version');

        if (version_compare($shopwareVersion, '5.2.17', '>=')) {
            $file = $this->getPath() . '/Resources/config/config.xml';
        } else {
            // For Shopware <= 5.2.17 we load a different config file due to missing `button` element in configuration
            $file = $this->getPath() . '/Resources/config/config_compat.xml';
        }

        if (class_exists(XmlConfigReader::class)) {
            $xmlConfigReader = new XmlConfigReader();
        } else {
            $xmlConfigReader = new \Shopware\Components\Plugin\XmlConfigDefinitionReader();
        }

        $config = $xmlConfigReader->read($file);

        /** @var InstallerService $pluginManager */
        $pluginManager = Shopware()->Container()->get('shopware_plugininstaller.plugin_manager');
        $plugin = $pluginManager->getPluginByName('FinSearchUnified');
        $formSynchronizer = new FormSynchronizer(Shopware()->Models());
        $formSynchronizer->synchronize($plugin, $config);
    }

    public function update(UpdateContext $context)
    {
        if (version_compare($context->getCurrentVersion(), $context->getUpdateVersion(), '<')) {
            $context->scheduleClearCache([UpdateContext::CACHE_TAG_THEME]);
        }
        parent::update($context);
    }

    /**
     * Try to deactivate any customization plugins of FINDOLOGIC
     */
    private function deactivateCustomizedPlugin()
    {
        /** @var InstallerService $pluginManager */
        $pluginManager = Shopware()->Container()->get('shopware_plugininstaller.plugin_manager');

        try {
            /** @var Models\Plugin\Plugin $plugin */
            $plugin = $pluginManager->getPluginByName('ExtendFinSearchUnified');
            if ($plugin->getActive()) {
                $pluginManager->deactivatePlugin($plugin);
            }
        } catch (Exception $exception) {
            Shopware()->PluginLogger()->info("ExtendFinSearchUnified plugin doesn't exist!");
        }
    }

    /**
     * @return string
     */
    public function getContainerPrefix()
    {
        return $this->camelCaseToUnderscore($this->getName());
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function camelCaseToUnderscore($string)
    {
        return strtolower(ltrim(preg_replace('/[A-Z]/', '_$0', $string), '_'));
    }
}
