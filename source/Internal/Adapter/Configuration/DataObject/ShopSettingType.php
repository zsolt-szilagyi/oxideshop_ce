<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject;

/**
 * @internal
 */
class ShopSettingType
{
    const BOOLEAN           = 'bool';
    const ARRAY             = 'arr';
    const ASSOCIATIVE_ARRAY = 'aarr';
    const INTEGER           = 'int';
    const STRING            = 'str';
    const PASSWORD          = 'password';
    const SELECT            = 'select';
}
