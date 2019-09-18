<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Database\Logger;

/**
 * @internal
 */
interface DatabaseLoggerFactoryInterface
{
    /**
     * @return null|\Doctrine\DBAL\Logging\SQLLogger
     */
    public function getDatabaseLogger();
}
