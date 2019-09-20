<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Database\Logger\AdminLogger;
use OxidEsales\EshopCommunity\Internal\Database\Logger\QueryFilter;
use Psr\Log\LoggerInterface;

class AdminLoggerTest extends \PHPUnit\Framework\TestCase
{
    public function providerTestLogging()
    {
        $data = [
             [
                 'query_pass' => true,
                 'expected'   => 'once'
             ],
             [
                 'query_pass' => false,
                 'expected'   => 'never'
             ]
        ];

        return $data;
    }

    /**
     * @param bool   $queryPass
     * @param string $expected
     *
     * @dataProvider providerTestLogging
     */
    public function testLogging(bool $queryPass, string $expected)
    {
        $context = new DatabaseLoggerContextStub();
        $context->setIsEnabledAdminQueryLog(true);
        $context->setIsAdmin(true);
        $context->setUserId('_myTestUser');

        $queryFilter = $this->getQueryFilterMock($queryPass);
        $psrLogger = $this->getPsrLoggerMock();

        $psrLogger->expects($this->$expected())
            ->method('debug');

        $logger = new AdminLogger($queryFilter, $context, $psrLogger);
        $query = 'dummy test query where oxid = :id ';

        $logger->startQuery($query, [':id' => 'testid']);
        $logger->stopQuery();
    }

    /**
     * Test helper.
     *
     * @param bool $pass
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|QueryFilter
     */
    private function getQueryFilterMock($pass = true)
    {
        $queryFilter = $this->getMockBuilder(QueryFilter::class)
            ->setMethods(['shouldLogQuery'])
            ->getMock();

        $queryFilter->expects($this->any())
            ->method('shouldLogQuery')
            ->willReturn($pass);

        return $queryFilter;
    }

    /**
     * Test helper.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private function getPsrLoggerMock()
    {
        $psrLogger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'emergency',
                    'alert',
                    'critical',
                    'error',
                    'warning',
                    'notice',
                    'info',
                    'debug',
                    'log'
                ]
            )
            ->getMock();

        return $psrLogger;
    }
}
