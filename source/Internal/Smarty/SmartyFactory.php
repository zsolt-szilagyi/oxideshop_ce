<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Creates and configures the Smarty object.
 */
class SmartyFactory implements SmartyFactoryInterface
{
    /**
     * @var \Smarty
     */
    private $smarty;

    /**
     * @var SmartyEngineConfigurationInterface
     */
    private $configuration;

    /**
     * SmartyFactory constructor.
     *
     * @param SmartyEngineConfigurationInterface $configuration
     */
    public function __construct(SmartyEngineConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return \Smarty
     */
    public function getSmarty() : \Smarty
    {
        if (!isset($this->smarty)) {
            $this->buildSmarty();
        }

        return $this->smarty;
    }

    /**
     * @param \Smarty $smarty
     */
    public function setSmarty(\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * Create new smarty instance and configure it
     */
    private function buildSmarty()
    {
        $this->smarty = new \Smarty();
        $this->setSettings();
        $this->setSecuritySettings();
        $this->registerPlugins();
        $this->registerPrefilters();
        $this->registerResources();
    }

    /**
     * Sets properties of smarty object.
     */
    private function setSettings()
    {
        $type = SmartyEngineConfigurationInterface::BASIC_SETTINGS;
        if ($this->configuration->hasConfiguration($type)) {
            foreach ($this->configuration->getConfiguration($type) as $key => $value) {
                $this->smarty->$key = $value;
            }
        }
    }

    /**
     * Sets security options of smarty object.
     */
    private function setSecuritySettings()
    {
        $type = SmartyEngineConfigurationInterface::SECURITY_SETTINGS;
        if ($this->configuration->hasConfiguration($type)) {
            $settings = $this->configuration->getConfiguration($type);
            foreach ($settings as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subValue) {
                        $this->smarty->security_settings[$key][] = $subValue;
                    }
                } else {
                    $this->smarty->$key = $value;
                }
            }
        }
    }

    /**
     * Registers a resource of smarty object.
     */
    private function registerResources()
    {
        $type = SmartyEngineConfigurationInterface::RESOURCES;
        if ($this->configuration->hasConfiguration($type)) {
            $resourcesToRegister = $this->configuration->getConfiguration($type);
            foreach ($resourcesToRegister as $key => $resources) {
                $this->smarty->register_resource($key, $resources);
            }
        }
    }

    /**
     * Register prefilters of smarty object.
     */
    private function registerPrefilters()
    {
        $type = SmartyEngineConfigurationInterface::PREFILTER;
        if ($this->configuration->hasConfiguration($type)) {
            $prefilters = $this->configuration->getConfiguration($type);
            foreach ($prefilters as $prefilter => $path) {
                if (file_exists($path)) {
                    include_once $path;
                    $this->smarty->register_prefilter($prefilter);
                }
            }
        }
    }

    /**
     * Register plugins of smarty object.
     */
    private function registerPlugins()
    {
        $type = SmartyEngineConfigurationInterface::PLUGINS;
        if ($this->configuration->hasConfiguration($type)) {
            $this->smarty->plugins_dir = array_merge(
                $this->configuration->getConfiguration($type),
                $this->smarty->plugins_dir
            );
        }
    }
}
