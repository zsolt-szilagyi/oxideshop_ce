<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;


use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ShopConfiguration;

/**
 * Class ShopConfigurationMapper
 *
 * @package OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper
 */
class ShopConfigurationMapper
{

    /**
     * @param $object
     *
     * @return array
     */
    public function toData($object): array
    {
        return [];
    }

    /**
     * @param array $data
     *
     * @return mixed|ShopConfiguration
     */
    public function fromData(array $data)
    {
        return new ShopConfiguration();
    }
}