<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\BooleanSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingFactory;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\StringSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ShopModuleSettingModuleSettingHandler;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShopModuleSettingModuleSettingHandlerTest extends TestCase
{
    public function testHandlingOnModuleActivation()
    {
        $moduleSetting = $this->getTestModuleSetting();

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addSetting($moduleSetting);

        $shopModuleSetting = $this->getTestShopModuleSetting();

        $shopModuleSettingDao = $this->getMockBuilder(SettingDaoInterface::class)->getMock();
        $shopModuleSettingDao
            ->expects($this->once())
            ->method('save')
            ->with($shopModuleSetting);

        $handler = new ShopModuleSettingModuleSettingHandler($shopModuleSettingDao, new SettingFactory());
        $handler->handleOnModuleActivation($moduleConfiguration, 1);
    }

    public function testHandlingOnModuleDeactivation()
    {
        $moduleSetting = $this->getTestModuleSetting();

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addSetting($moduleSetting);

        $shopModuleSetting = $this->getTestShopModuleSetting();

        $shopModuleSettingDao = $this->getMockBuilder(SettingDaoInterface::class)->getMock();
        $shopModuleSettingDao
            ->expects($this->once())
            ->method('delete')
            ->with($shopModuleSetting);

        $handler = new ShopModuleSettingModuleSettingHandler($shopModuleSettingDao, new SettingFactory());
        $handler->handleOnModuleDeactivation($moduleConfiguration, 1);
    }

    private function getTestModuleSetting(): ModuleSetting
    {
        return new ModuleSetting(
            ModuleSetting::SHOP_MODULE_SETTING,
            [
                [
                    'name'          => 'blCustomGridFramework',
                    'type'          => 'str',
                    'value'         => 'string',
                    'constraints'   => ['1', '2', '3',],
                    'group'         => 'frontend',
                    'position'      => 5,
                ],
            ]
        );
    }

    private function getTestShopModuleSetting(): SettingInterface
    {
        $setting = new StringSetting('blCustomGridFramework', 'string');
        $setting->setConstraints(['1', '2', '3',]);
        $setting->setGroupName('frontend');
        $setting->setPositionInGroup(5);

        return $setting;
    }
}
