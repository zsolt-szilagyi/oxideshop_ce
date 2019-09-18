<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Database\Logger;

/**
 * @internal
 */
interface DatabaseLoggerContextInterface
{
    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @return int
     */
    public function getShopId(): int;

    /**
     * @return string
     */
    public function getLogLevel(): string;

    /**
     * @return string
     */
    public function getLogFilePath(): string;

    /**
     * @return array
     */
    public function getSkipLogTags(): array;

    /**
     * @return bool
     */
    public function isEnabledAdminQueryLog(): bool;

    /**
     * @return string
     */
    public function getUserId(): string;
}
