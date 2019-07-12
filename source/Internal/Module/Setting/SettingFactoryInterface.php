<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setting;

/**
 * @internal
 */
interface SettingFactoryInterface
{
    public function create(string $type, string $name, $value): SettingInterface;
}
