<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Smarty;


use org\bovigo\vfs\vfsStream;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Internal\Smarty\Bridge\ModuleSmartyPluginBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContext;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngineConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyFactory;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyEngineConfiguration;

class SmartyFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider smartySettingsDataProvider
     *
     * @param bool  $securityMode
     * @param array $smartySettings
     */
    public function testSmartyPropertiesAreSetCorrect($securityMode, $smartySettings)
    {
        $smartyContext = $this->getSmartyContextMock($securityMode);
        $bridgeMock = $this->getModuleSmartyPluginBridgeMock();
        $smartyFactory = new SmartyFactory(new SmartyEngineConfiguration($smartyContext, $bridgeMock));

        $smarty = $smartyFactory->getSmarty();

        foreach ($smartySettings as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName . ' setting was not set');
            $this->assertEquals($varValue, $smarty->$varName, 'Not correct value of the smarts setting: ' . $varName);
        }
    }

    /**
     * @return array
     */
    public function smartySettingsDataProvider()
    {
        return [
                'security on' => [true, $this->getSmartySettingsWithSecurityOn()],
                'security off' => [false, $this->getSmartySettingsWithSecurityOff()]
        ];
    }

    private function getSmartySettingsWithSecurityOn(): array
    {
        return [
            'security' => true,
            'php_handling' => SMARTY_PHP_REMOVE,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => "testCompileDir/smarty/",
            'cache_dir' => "testCompileDir/smarty/",
            'compile_id' => "7f96e0d92070fd4733296e5118fd5a01",
            'template_dir' => ["testTemplateDir"],
            'debugging' => true,
            'compile_check' => true,
            'security_settings' => [
                'PHP_HANDLING' => false,
                'IF_FUNCS' =>
                    [
                        0 => 'array',
                        1 => 'list',
                        2 => 'isset',
                        3 => 'empty',
                        4 => 'count',
                        5 => 'sizeof',
                        6 => 'in_array',
                        7 => 'is_array',
                        8 => 'true',
                        9 => 'false',
                        10 => 'null',
                        11 => 'XML_ELEMENT_NODE',
                        12 => 'is_int',
                    ],
                'INCLUDE_ANY' => false,
                'PHP_TAGS' => false,
                'MODIFIER_FUNCS' =>
                    [
                        0 => 'count',
                        1 => 'round',
                        2 => 'floor',
                        3 => 'trim',
                        4 => 'implode',
                        5 => 'is_array',
                        6 => 'getimagesize',
                    ],
                'ALLOW_CONSTANTS' => true,
                'ALLOW_SUPER_GLOBALS' => true,
            ],
            'plugins_dir' => [
                'testModuleDir',
                'testShopDir/Core/Smarty/Plugin',
                'plugins'
            ],
        ];
    }

    private function getSmartySettingsWithSecurityOff(): array
    {
        return [
            'security' => false,
            'php_handling' => 1,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => "testCompileDir",
            'cache_dir' => "testCompileDir",
            'compile_id' => "7f96e0d92070fd4733296e5118fd5a01",
            'template_dir' => ["testTemplateDir"],
            'debugging' => true,
            'compile_check' => true,
            'plugins_dir' => [
                'testModuleDir',
                'testShopDir/Core/Smarty/Plugin',
                'plugins'
            ],
        ];
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

    private function getSmartyContextMock($securityMode = false): SmartyContext
    {
        $config = $this->getConfigMock($securityMode);
        $utilsView = $this->getUtilsViewMock();

        $smartyContextMock = $this
            ->getMockBuilder(SmartyContext::class)
            ->setConstructorArgs([$config, $utilsView])
            ->setMethods(['getSourcePath'])
            ->getMock();

        $smartyContextMock
            ->method('getSourcePath')
            ->willReturn('testShopDir');

        return $smartyContextMock;
    }

    private function getConfigMock($demoShopMode = false): Config
    {
        $structure = [
            'Smarty' => [
                'Plugin' => 'prefilter.oxblock.php'
            ]
        ];
        $smartyDir = vfsStream::setup('testDir', null, $structure);

        $configMock = $this
            ->getMockBuilder(Config::class)
            ->getMock();

        $configMock->expects($this->at(0))
            ->method('getConfigParam')
            ->with('sCompileDir')
            ->will($this->returnValue('testCompileDir'));

        $configMock->expects($this->at(1))
            ->method('getConfigParam')
            ->with('iDebug')
            ->will($this->returnValue(1));

        $configMock->expects($this->at(2))
            ->method('getConfigParam')
            ->with('iDebug')
            ->will($this->returnValue(1));

        $configMock->expects($this->at(3))
            ->method('getConfigParam')
            ->with('blCheckTemplates')
            ->will($this->returnValue(true));

        $configMock->expects($this->at(4))
            ->method('getConfigParam')
            ->with('iSmartyPhpHandling')
            ->will($this->returnValue(true));

        $configMock->expects($this->at(5))
            ->method('getConfigParam')
            ->with('sCoreDir')
            ->will($this->returnValue($smartyDir->url().'Smarty/Plugin'));

        $configMock->method('getShopId')
            ->will($this->returnValue(1));

        $configMock->method('isDemoShop')
            ->will($this->returnValue($demoShopMode));

        $configMock->method('isAdmin')
            ->will($this->returnValue(false));

        return $configMock;
    }

    private function getUtilsViewMock(): UtilsView
    {
        $utilsViewMock = $this
            ->getMockBuilder(UtilsView::class)
            ->getMock();

        $utilsViewMock->method('getTemplateDirs')
            ->will($this->returnValue(['testTemplateDir']));

        return $utilsViewMock;
    }
}