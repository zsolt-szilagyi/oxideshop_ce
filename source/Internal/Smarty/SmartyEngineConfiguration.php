<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\EshopCommunity\Internal\Smarty\Bridge\ModuleSmartyPluginBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\Extension\CacheResourcePlugin;
use OxidEsales\EshopCommunity\Internal\Smarty\Extension\SmartyDefaultTemplateHandler;

/**
 * Class SmartyEngineConfiguration
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyEngineConfiguration implements SmartyEngineConfigurationInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * @var ModuleSmartyPluginBridgeInterface
     */
    private $bridge;

    /**
     * @var array
     */
    private $configuration;

    /**
     * TemplateEngineConfiguration constructor.
     *
     * @param SmartyContextInterface            $context
     * @param ModuleSmartyPluginBridgeInterface $bridge
     */
    public function __construct(SmartyContextInterface $context, ModuleSmartyPluginBridgeInterface $bridge)
    {
        $this->context = $context;
        $this->bridge = $bridge;
        $this->configuration = [];

        $this->setSettings();
        $this->setSecuritySettings();
        $this->setPlugins();
        $this->setPrefilterPlugin();
        $this->setResources();
    }

    /**
     * Define basic smarty settings
     */
    private function setSettings()
    {
        $compilePath = $this->getTemplateCompilePath();
        $this->configuration[self::BASIC_SETTINGS] = [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => $compilePath,
            'cache_dir' => $compilePath,
            'template_dir' => $this->context->getTemplateDirectories(),
            'compile_id' => $this->getTemplateCompileId(),
            'default_template_handler_func' => [new SmartyDefaultTemplateHandler($this->context), 'handleTemplate'],
            'debugging' => $this->context->getTemplateEngineDebugMode(),
            'compile_check' => $this->context->getTemplateCompileCheckMode()
        ];
    }

    /**
     * Define smarty security settings.
     */
    private function setSecuritySettings()
    {
        $this->configuration[self::SECURITY_SETTINGS] = [
            'php_handling' => (int) $this->context->getTemplatePhpHandlingMode(),
            'security' => false
        ];
        if ($this->context->getTemplateSecurityMode()) {
            $this->configuration[self::SECURITY_SETTINGS] = [
                'php_handling' => SMARTY_PHP_REMOVE,
                'security' => true,
                'secure_dir' => $this->context->getTemplateDirectories(),
                'security_settings' => [
                    'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                    'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                    'ALLOW_CONSTANTS' => true,
                ]
            ];
        }
    }

    /**
     * Collect smarty plugins.
     */
    private function setPlugins()
    {
        $this->configuration[self::PLUGINS] = array_merge(
            $this->bridge->getModuleSmartyPluginPaths(),
            $this->getShopPluginPaths()
        );
    }

    /**
     * Sets an array of prefilters.
     */
    private function setPrefilterPlugin()
    {
        $prefilterPath = $this->getPrefilterPath();
        $prefilter['smarty_prefilter_oxblock'] = $prefilterPath . '/prefilter.oxblock.php';
        if ($this->context->showTemplateNames()) {
            $prefilter['smarty_prefilter_oxtpldebug'] = $prefilterPath . '/prefilter.oxtpldebug.php';
        }

        $this->configuration[self::PREFILTER] = $prefilter;
    }

    /**
     * Sets an array of resources.
     */
    private function setResources()
    {
        $resource = new CacheResourcePlugin($this->context);
        $this->configuration[self::RESOURCES] = [
            'ox' => [
                $resource,
                'getTemplate',
                'getTimestamp',
                'getSecure',
                'getTrusted'
            ]
        ];
    }

    /**
     * @return string
     */
    private function getPrefilterPath() : string
    {
        return $this->context->getSourcePath() . '/Core/Smarty/Plugin';
    }

    /**
     * Returns a full path to Smarty compile dir
     *
     * @return string
     */
    private function getTemplateCompilePath() : string
    {
        //check for the Smarty dir
        $compileDir = $this->context->getShopCompileDirectory();
        $smartyCompileDir = $compileDir . "/smarty/";

        if (!is_dir($smartyCompileDir)) {
            @mkdir($smartyCompileDir);
        }

        if (!is_writable($smartyCompileDir)) {
            $smartyCompileDir = $compileDir;
        }

        return $smartyCompileDir;
    }

    /**
     * Get template compile id.
     *
     * @return string
     */
    private function getTemplateCompileId() : string
    {
        $shopId = $this->context->getCurrentShopId();
        $templatePath = $this->context->getTemplateDirectories();

        return md5(reset($templatePath) . '__' . $shopId);
    }

    /**
     * @return array
     */
    private function getShopPluginPaths() : array
    {
        return [$this->context->getSourcePath() . '/Core/Smarty/Plugin'];
    }

    /**
     * @param string $type
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getConfiguration(string $type, $default = null)
    {
        return array_key_exists($type, $this->configuration) ? $this->configuration[$type] : $default;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasConfiguration(string $type): bool
    {
        return isset($this->configuration[$type]);
    }
}
