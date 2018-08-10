<?php
/**
 * Created by PhpStorm.
 * User: vilma
 * Date: 06.08.18
 * Time: 16:02
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;


class TemplateEngineConfiguration implements TemplateEngineConfigurationInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * TemplateEngineConfiguration constructor.
     *
     * @param SmartyContextInterface $context
     */
    public function __construct(SmartyContextInterface $context)
    {
        $this->context = $context;
    }

    public function getOptions()
    {
        return [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => $this->context->getTemplateCompileDirectory(),
            'cache_dir' => $this->context->getTemplateCompileDirectory(),
            'template_dir' => $this->context->getTemplateDirectories(),
            'compile_id' => $this->context->getTemplateCompileId(),
            'default_template_handler_func' => [\OxidEsales\Eshop\Core\Registry::getUtilsView(), '_smartyDefaultTemplateHandler'],
            'debugging' => $this->getDebuggMode(),
            'compile_check' => $this->context->getTemplateCompileCheck()
        ];
    }

    public function getSecurityOptions()
    {
        $options = [
            'php_handling' => (int) $this->context->getTemplatePhpHandlingMode(),
            'security' => false
        ];
        if ($this->context->getTemplateSecurityMode()) {
            $options = [
                'php_handling' => SMARTY_PHP_REMOVE,
                'security' => true,
                'secure_dir' => $this->context->getTemplateDirectories(),
                'security_settings' => $this->getSecuritySettings()
            ];
        }
        return $options;
    }

    public function getPlugins()
    {
        return array_merge(
            $this->context->getModuleTemplatePluginDirectories(),
            $this->context->getShopTemplatePluginDirectories()
        );
    }

    public function getPrefilterPlugin()
    {
        $shopSmartyPluginPath = $this->context->getShopTemplatePluginDirectory() ;
        $prefilter['smarty_prefilter_oxblock'] = $shopSmartyPluginPath . '/prefilter.oxblock.php';
        if ($this->context->showTemplateNames()) {
            $prefilter['smarty_prefilter_oxtpldebug'] = $shopSmartyPluginPath . '/prefilter.oxtpldebug.php';
        }

        return $prefilter;
    }

    public function getResources()
    {
        return ['ox' => [
                    oxSmarty::class,
                    'ox_get_template',
                    'ox_get_timestamp',
                    'ox_get_secure',
                    'ox_get_trusted'
                    ]
                ];
    }

    private function getSecuritySettings()
    {
        return [
            'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
            'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
            'ALLOW_CONSTANTS' => true,
        ];
    }

    private function getDebuggMode()
    {
        return $this->context->getTemplateEngineDebugMode();
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        $params['options'] = $this->getOptions();
        $params['securityOptions'] = $this->getSecurityOptions();
        $params['plugins'] = $this->getPlugins();
        $params['prefilters'] = $this->getPrefilterPlugin();
        $params['resources'] = $this->getResources();
        return $params;
    }
}