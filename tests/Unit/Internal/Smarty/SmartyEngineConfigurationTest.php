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
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyConfigurationFactory;

class SmartyConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigurationWithSecuritySettingsOff()
    {
        $smartyContextMock = $this->getSmartyContextMock();
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();

        $factory = new SmartyConfigurationFactory($smartyContextMock, $bridgeMock);
        $configuration = $factory->getConfiguration();

        $this->assertSettings($configuration);
        $this->assertSecuritySettingsOff($configuration);
        $this->assertPrefilters($configuration);
        $this->assertPlugins($configuration);
        $this->assertResources($configuration);
    }

    public function testGetConfigurationWithSecuritySettingsOn()
    {
        $smartyContextMock = $this->getSmartyContextMock(true);
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();

        $factory = new SmartyConfigurationFactory($smartyContextMock, $bridgeMock);
        $configuration = $factory->getConfiguration();

        $this->assertSettings($configuration);
        $this->assertSecuritySettingsOn($configuration);
        $this->assertPrefilters($configuration);
        $this->assertPlugins($configuration);
        $this->assertResources($configuration);
    }

    private function assertSettings(array $configuration)
    {
        $smartyContextMock = $this->getSmartyContextMock();
        $settings = [
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

        $this->assertEquals($settings, $configuration['settings']);
    }

    private function assertSecuritySettingsOff(array $configuration)
    {
        $settings = [
            'php_handling' => 1,
            'security' => false
        ];
        $this->assertEquals($settings, $configuration['security_settings']);
    }

    private function assertSecuritySettingsOn(array $configuration)
    {
        $settings = [
            'php_handling' => SMARTY_PHP_REMOVE,
            'security' => true,
            'secure_dir' => ['testTemplateDir'],
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
                ]
            ];

        $this->assertEquals($settings, $configuration['security_settings']);
    }

    private function assertResources(array $configuration)
    {
        $smartyContextMock = $this->getSmartyContextMock();
        $resource = new CacheResourcePlugin($smartyContextMock);
        $settings = ['ox' => [
            $resource,
            'getTemplate',
            'getTimestamp',
            'getSecure',
            'getTrusted'
            ]
        ];

        $this->assertEquals($settings, $configuration['resources']);
    }

    private function assertPrefilters(array $configuration)
    {
        $settings = [
            'smarty_prefilter_oxblock' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxblock.php',
            'smarty_prefilter_oxtpldebug' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxtpldebug.php',
        ];

        $this->assertEquals($settings, $configuration['prefilters']);
    }

    private function assertPlugins(array $configuration)
    {
        $settings = ['testModuleDir', 'testShopPath/Core/Smarty/Plugin'];

        $this->assertEquals($settings, $configuration['plugins']);
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