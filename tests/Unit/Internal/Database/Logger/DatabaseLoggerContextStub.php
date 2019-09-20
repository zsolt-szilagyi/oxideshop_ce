<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Database\Logger\DatabaseLoggerContextInterface;

/**
 * @internal
 */
class DatabaseLoggerContextStub implements DatabaseLoggerContextInterface
{
    private $logLevel;
    private $logFilePath;
    private $shopId;
    private $skipLogTags;
    private $doLogAdminQueries;
    private $userId;
    private $isAdmin;

    /**
     * ContextStub constructor.
     */
    public function __construct()
    {
        $context = ContainerFactory::getInstance()->getContainer()->get(DatabaseLoggerContextInterface::class);

        $this->logLevel = $context->getLogLevel();
        $this->shopId = $context->getShopId();
        $this->logFilePath = $context->getLogFilePath();
        $this->skipLogTags = $context->getSkipLogTags();
        $this->doLogAdminQueries = $context->isEnabledAdminQueryLog();
        $this->userId = $context->getUserId();
        $this->isAdmin = $context->isAdmin();
    }

    /**
     * @param string $logLevel
     */
    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * @param string $logFilePath
     */
    public function setLogFilePath($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        return $this->logFilePath;
    }

    /**
     * @param int $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @param array $skipLogTags
     */
    public function setSkipLogTags(array $skipLogTags)
    {
        $this->skipLogTags = $skipLogTags;
    }

    /**
     * @return array
     */
    public function getSkipLogTags(): array
    {
        return $this->skipLogTags;
    }

    /**
     * @param bool $doLogAdminQueries
     */
    public function setIsEnabledAdminQueryLog(bool $doLogAdminQueries)
    {
       $this->doLogAdminQueries = $doLogAdminQueries;
    }

    /**
     * @return bool
     */
    public function isEnabledAdminQueryLog(): bool
    {
        return $this->doLogAdminQueries;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }
}
