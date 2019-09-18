<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Logger\Configuration\MonologConfiguration;
use OxidEsales\EshopCommunity\Internal\Logger\Configuration\MonologConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Logger\Factory\MonologLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Logger\Wrapper\LoggerWrapper;
use OxidEsales\EshopCommunity\Internal\Logger\Validator\PsrLoggerConfigurationValidator;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
class DatabaseLoggerFactory implements DatabaseLoggerFactoryInterface
{

    /**
     * @var DatabaseLoggerContextInterface
     */
    private $context;

    /**
     * DatabaseLoggerFactory constructor.
     *
     * @param DatabaseLoggerContextInterface $context
     */
    public function __construct(DatabaseLoggerContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return null|\Doctrine\DBAL\Logging\SQLLogger
     */
    public function getDatabaseLogger()
    {
        $logger = null;

        if ($this->context->isAdmin() && $this->context->isEnabledAdminQueryLog()) {
            $logger = $this->getAdminLogger();
        }

        return $logger;
    }

    /**
     * @return QueryFilter
     */
    private function getQueryFilter()
    {
        return new QueryFilter();
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        $factory = new PsrLoggerFactory($this->context);
        return $factory->getLogger();
    }

    /**
     * @return AdminLogger
     */
    private function getAdminLogger()
    {
        return new AdminLogger($this->getQueryFilter(), $this->context, $this->getLogger());
    }
}
