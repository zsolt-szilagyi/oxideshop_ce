<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty;


use OxidEsales\EshopCommunity\Internal\Smarty\Bridge\ModuleSmartyPluginBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\Extension\CacheResourcePlugin;
use OxidEsales\EshopCommunity\Internal\Smarty\Extension\SmartyDefaultTemplateHandler;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngineConfiguration;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngineConfigurationInterface;

class SmartyEngineConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetSettings()
    {
        $smartyContextMock = $this->getSmartyContextMock();
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();
        $options = [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => 'testCompileDir',
            'cache_dir' => 'testCompileDir',
            'template_dir' => ['testTemplateDir'],
            'compile_id' => '7f96e0d92070fd4733296e5118fd5a01',
            'default_template_handler_func' => [new SmartyDefaultTemplateHandler($smartyContextMock), 'handleTemplate'],
            'debugging' => true,
            'compile_check' => true
        ];

        $configuration = new SmartyEngineConfiguration($smartyContextMock, $bridgeMock);
        $this->assertTrue($configuration->hasConfiguration(SmartyEngineConfigurationInterface::BASIC_SETTINGS));
        $this->assertEquals($options, $configuration->getConfiguration(SmartyEngineConfigurationInterface::BASIC_SETTINGS));
    }

    public function testGetSecuritySettingsIfOff()
    {
        $options = [
            'php_handling' => 1,
            'security' => false
        ];

        $smartyContextMock = $this->getSmartyContextMock();
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock, $bridgeMock);
        $this->assertTrue($configuration->hasConfiguration(SmartyEngineConfigurationInterface::SECURITY_SETTINGS));
        $this->assertEquals($options, $configuration->getConfiguration(SmartyEngineConfigurationInterface::SECURITY_SETTINGS));
    }

    public function testGetSecuritySettingsIfOn()
    {
        $options = [
            'php_handling' => SMARTY_PHP_REMOVE,
            'security' => true,
            'secure_dir' => ['testTemplateDir'],
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
                ]
            ];

        $smartyContextMock = $this->getSmartyContextMock(true);
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock, $bridgeMock);
        $this->assertTrue($configuration->hasConfiguration(SmartyEngineConfigurationInterface::SECURITY_SETTINGS));
        $this->assertEquals($options, $configuration->getConfiguration(SmartyEngineConfigurationInterface::SECURITY_SETTINGS));
    }

    public function testGetResources()
    {
        $smartyContextMock = $this->getSmartyContextMock();
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();
        $resource = new CacheResourcePlugin($smartyContextMock);
        $options = ['ox' => [
            $resource,
            'getTemplate',
            'getTimestamp',
            'getSecure',
            'getTrusted'
            ]
        ];

        $configuration = new SmartyEngineConfiguration($smartyContextMock, $bridgeMock);
        $this->assertTrue($configuration->hasConfiguration(SmartyEngineConfigurationInterface::RESOURCES));
        $this->assertEquals($options, $configuration->getConfiguration(SmartyEngineConfigurationInterface::RESOURCES));
    }

    public function testGetPrefilterPlugin()
    {
        $options = [
            'smarty_prefilter_oxblock' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxblock.php',
            'smarty_prefilter_oxtpldebug' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxtpldebug.php',
        ];

        $smartyContextMock = $this->getSmartyContextMock();
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock, $bridgeMock);
        $this->assertTrue($configuration->hasConfiguration(SmartyEngineConfigurationInterface::PREFILTER));
        $this->assertEquals($options, $configuration->getConfiguration(SmartyEngineConfigurationInterface::PREFILTER));
    }

    public function testGetPlugin()
    {
        $options = ['testModuleDir', 'testShopPath/Core/Smarty/Plugin'];

        $smartyContextMock = $this->getSmartyContextMock();
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();
        $configuration = new SmartyEngineConfiguration($smartyContextMock, $bridgeMock);
        $this->assertTrue($configuration->hasConfiguration(SmartyEngineConfigurationInterface::PLUGINS));
        $this->assertEquals($options, $configuration->getConfiguration(SmartyEngineConfigurationInterface::PLUGINS));
    }

    private function getSmartyContextMock($securityMode = false): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateEngineDebugMode')
            ->willReturn('2');

        $smartyContextMock
            ->method('showTemplateNames')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplateSecurityMode')
            ->willReturn($securityMode);

        $smartyContextMock
            ->method('getShopCompileDirectory')
            ->willReturn('testCompileDir');

        $smartyContextMock
            ->method('getTemplateDirectories')
            ->willReturn(['testTemplateDir']);

        $smartyContextMock
            ->method('getTemplateCompileCheckMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplatePhpHandlingMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getCurrentShopId')
            ->willReturn(1);

        $smartyContextMock
            ->method('getSourcePath')
            ->willReturn('testShopPath');

        return $smartyContextMock;
    }

    private function getModuleSmartyPluginBridgeMock(): ModuleSmartyPluginBridgeInterface
    {
        $bridgeMock = $this
            ->getMockBuilder(ModuleSmartyPluginBridgeInterface::class)
            ->getMock();

        $bridgeMock
            ->method('getModuleSmartyPluginPaths')
            ->willReturn(['testModuleDir']);

        return $bridgeMock;
    }
}