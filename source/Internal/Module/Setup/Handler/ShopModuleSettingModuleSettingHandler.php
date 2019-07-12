<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\ShopModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ShopModuleSettingModuleSettingHandler implements ModuleConfigurationHandlerInterface
{
    /**
     * @var SettingDaoInterface
     */
    private $settingDao;

    /**
     * @var SettingFactoryInterface
     */
    private $settingFactory;

    /**
     * @param SettingDaoInterface $settingDao
     * @param SettingFactoryInterface $settingFactory
     */
    public function __construct(SettingDaoInterface $settingDao, SettingFactoryInterface $settingFactory)
    {
        $this->settingDao = $settingDao;
        $this->settingFactory = $settingFactory;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleActivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($this->canHandle($configuration)) {
            $setting = $configuration->getSetting(ModuleSetting::SHOP_MODULE_SETTING);

            foreach ($setting->getValue() as $shopModuleSettingData) {
                $moduleSetting = $this->mapDataToShopModuleSetting($shopModuleSettingData);

                $this->settingDao->save($moduleSetting, $configuration->getId(), $shopId);
            }
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function handleOnModuleDeactivation(ModuleConfiguration $configuration, int $shopId)
    {
        if ($this->canHandle($configuration)) {
            $setting = $configuration->getSetting(ModuleSetting::SHOP_MODULE_SETTING);

            foreach ($setting->getValue() as $shopModuleSettingData) {
                $moduleSetting = $this->mapDataToShopModuleSetting($shopModuleSettingData);

                $this->settingDao->delete($moduleSetting, $configuration->getId(), $shopId);
            }
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     * @return bool
     */
    private function canHandle(ModuleConfiguration $configuration): bool
    {
        return $configuration->hasSetting(ModuleSetting::SHOP_MODULE_SETTING);
    }

    /**
     * @param array             $data
     * @return SettingInterface
     */
    private function mapDataToShopModuleSetting(array $data): SettingInterface
    {
        $setting = $this->settingFactory->create(
            $data['type'],
            $data['name'],
            $data['value']
        );

        if (isset($data['constraints'])) {
            $setting->setConstraints($data['constraints']);
        }

        if (isset($data['group'])) {
            $setting->setGroupName($data['group']);
        }

        if (isset($data['position'])) {
            $setting->setPositionInGroup($data['position']);
        }

        return $setting;
    }
}
