<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setting;

/**
 * @internal
 */
interface SettingDaoInterface
{
    public function save(SettingInterface $setting, string $moduleId, int $shopId): void;

    public function delete(SettingInterface $setting, string $moduleId, int $shopId): void;

    public function get(string $name, string $moduleId, int $shopId): SettingInterface;
}
