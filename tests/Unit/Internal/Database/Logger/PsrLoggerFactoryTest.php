<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Database\Logger\PsrLoggerFactory;
use Psr\Log\LoggerInterface;

class PsrLoggerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreation()
    {
        $context = new DatabaseLoggerContextStub();

        $loggerFactory = new PsrLoggerFactory(
            $context
        );

        $this->assertInstanceOf(
            LoggerInterface::class,
            $loggerFactory->getLogger()
        );
    }
}
