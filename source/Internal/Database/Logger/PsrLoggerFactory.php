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
class PsrLoggerFactory implements PsrLoggerFactoryInterface
{
    /**
     * @var DatabaseLoggerContextInterface
     */
    private $context;

    public function __construct(DatabaseLoggerContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @param DatabaseLoggerContextInterface $context
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return new LoggerWrapper(
            $this->getMonologLoggerFactory()->create()
        );
    }

    /**
     * @return MonologLoggerFactory
     */
    private function getMonologLoggerFactory()
    {
        return new MonologLoggerFactory(
            $this->getMonologConfiguration(),
            $this->getLoggerConfigurationValidator()
        );
    }

    /**
     * @return MonologConfigurationInterface
     */
    private function getMonologConfiguration()
    {
        return new MonologConfiguration(
            'OXID Admin Logger',
            $this->context->getLogFilePath(),
            $this->context->getLogLevel()
        );
    }

    /**
     * @return PsrLoggerConfigurationValidator
     */
    private function getLoggerConfigurationValidator()
    {
        return new PsrLoggerConfigurationValidator();
    }
}
