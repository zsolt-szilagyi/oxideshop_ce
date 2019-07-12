<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setting;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopSettingType;

/**
 * @internal
 */
class SettingFactory implements SettingFactoryInterface
{
    public function create(string $type, string $name, $value): SettingInterface
    {
        if ($type === ShopSettingType::BOOLEAN) {
            return new BooleanSetting($name, $value);
        }

        if ($type === ShopSettingType::INTEGER) {
            return new IntegerSetting($name, $value);
        }

        if ($type === ShopSettingType::STRING) {
            return new StringSetting($name, $value);
        }

        if ($type === ShopSettingType::PASSWORD) {
            return new PasswordSetting($name, $value);
        }

        if ($type === ShopSettingType::SELECT) {
            return new SelectSetting($name, $value);
        }

        if ($type === ShopSettingType::ARRAY || $type === ShopSettingType::ASSOCIATIVE_ARRAY) {
            return new ArraySetting($name, $value);
        }
    }
}
