<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Database\Logger\DatabaseLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Database\Logger\AdminLogger;

class DatabaseLoggerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreationForAdminLogEnabled()
    {
        $context = new DatabaseLoggerContextStub();
        $context->setIsAdmin(true);
        $context->setIsEnabledAdminQueryLog(true);

        $loggerFactory = new DatabaseLoggerFactory(
            $context
        );

        $this->assertInstanceOf(
            AdminLogger::class,
            $loggerFactory->getDatabaseLogger()
        );
    }

    public function testCreationForAdminLogDisabled()
    {
        $context = new DatabaseLoggerContextStub();
        $context->setIsAdmin(true);
        $context->setIsEnabledAdminQueryLog(false);

        $loggerFactory = new DatabaseLoggerFactory(
            $context
        );

        $this->assertNotInstanceOf(
            AdminLogger::class,
            $loggerFactory->getDatabaseLogger()
        );
    }

    public function testCreationForNormalUser()
    {
        $context = new DatabaseLoggerContextStub();
        $context->setIsAdmin(false);
        $context->setIsEnabledAdminQueryLog(true);

        $loggerFactory = new DatabaseLoggerFactory(
            $context
        );

        $this->assertNotInstanceOf(
            AdminLogger::class,
            $loggerFactory->getDatabaseLogger()
        );
    }
}
