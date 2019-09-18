<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Database\Logger;

use Psr\Log\LoggerInterface;

/**
 * @internal
 */
interface PsrLoggerFactoryInterface
{
    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;
}
