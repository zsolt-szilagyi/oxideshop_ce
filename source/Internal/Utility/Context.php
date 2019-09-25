<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use PDO;
use Psr\Log\LogLevel;
use Webmozart\PathUtil\Path;
use OxidEsales\Eshop\Core\Registry;

/**
 * @internal
 */
class Context extends BasicContext implements ContextInterface
{
    /**
     * @var FactsConfigFile
     */
    private $factsConfigFile;

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return $this->getConfigParameter('sLogLevel') ?? LogLevel::ERROR;
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        return Path::join(Registry::getConfig()->getLogsDir(), 'oxideshop.log');
    }

    /**
     * @return array
     */
    public function getRequiredContactFormFields(): array
    {
        $contactFormRequiredFields = $this->getConfigParameter('contactFormRequiredFields');

        return $contactFormRequiredFields === null ? [] : $contactFormRequiredFields;
    }

    /**
     * @return int
     */
    public function getCurrentShopId(): int
    {
        $return = 1;
        if (Registry::instanceExists(\OxidEsales\Eshop\Core\Config::class)) {
            $return = Registry::getConfig()->getShopId();
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        $integerShopIds = [];

        foreach (Registry::getConfig()->getShopIds() as $shopId) {
            $integerShopIds[] = (int) $shopId;
        }

        return $integerShopIds;
    }

    /**
     * @return string
     */
    public function getConfigurationEncryptionKey(): string
    {
        return $this->getConfigParameter('sConfigKey');
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    private function getConfigParameter($name, $default = null)
    {
        $value = Registry::getConfig()->getConfigParam($name, $default);
        DatabaseProvider::getDb()->setFetchMode(PDO::FETCH_ASSOC);
        return $value;
    }
}
