<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty\Bridge;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\Eshop\Core\Module\ModuleSmartyPluginDirectoryRepository;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;
use OxidEsales\Eshop\Core\SubShopSpecificFileCache;
use OxidEsales\EshopCommunity\Core\FileCache;
use OxidEsales\EshopCommunity\Core\Registry;

class ModuleSmartyPluginBridge implements ModuleSmartyPluginBridgeInterface
{
    /**
     * @return array
     */
    public function getModuleSmartyPluginPaths() : array
    {
        $moduleSmartyPluginDirectoryRepository = $this->getSmartyPluginDirectoryRepository();
        $moduleSmartyPluginDirectories = $moduleSmartyPluginDirectoryRepository->get();

        return $moduleSmartyPluginDirectories->getWithFullPath();
    }

    /**
     * @return ModuleSmartyPluginDirectoryRepository
     */
    private function getSmartyPluginDirectoryRepository() : ModuleSmartyPluginDirectoryRepository
    {
        $shopIdCalculator = $this->getShopIdCalculator();
        $subShopSpecificCache = oxNew(
            SubShopSpecificFileCache::class,
            $shopIdCalculator
        );

        $moduleVariablesLocator = oxNew(
            ModuleVariablesLocator::class,
            $subShopSpecificCache,
            $shopIdCalculator
        );

        return oxNew(
            ModuleSmartyPluginDirectoryRepository::class,
            Registry::getConfig(),
            $moduleVariablesLocator,
            oxNew(Module::class)
        );
    }

    /**
     * @return ShopIdCalculator
     */
    private function getShopIdCalculator() : ShopIdCalculator
    {
        $moduleVariablesCache = oxNew(FileCache::class);
        return oxNew(ShopIdCalculator::class, $moduleVariablesCache);
    }
}