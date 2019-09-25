<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\EshopCommunity\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;
use PDO;
use Psr\Log\LogLevel;
use Webmozart\PathUtil\Path;
use OxidEsales\Facts\Config\ConfigFile as FactsConfigFile;
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

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return isAdmin();
    }

    /**
     * @return bool
     */
    public function isEnabledAdminQueryLog(): bool
    {
        return $this->getFactsConfigFile()->getVar('blLogChangesInAdmin');
    }

    /**
     * @return FactsConfigFile
     */
    private function getFactsConfigFile(): FactsConfigFile
    {
        if (!is_a($this->factsConfigFile, FactsConfigFile::class)) {
            $this->factsConfigFile = new FactsConfigFile();
        }

        return $this->factsConfigFile;
    }

    /**
     * @return string
     */
    public function getAdminLogFilePath(): string
    {
        return Path::join($this->getSourcePath(), 'log', 'oxadmin.log');
    }

    /**
     * We need to be careful when trying to fetch config parameters in this place as the
     * shop might still be bootstrapping.
     * The config must be already initialized before we can safely call Config::getConfigParam().
     *
     * @return array
     */
    public function getSkipLogTags(): array
    {
        $skipLogTags = [];
        if (Registry::instanceExists(\OxidEsales\Eshop\Core\Config::class)) {
            $skipLogTags = Registry::getConfig()->getConfigParam('aLogSkipTags');
        }

        return (array) $skipLogTags;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        $return = '';
        if (Registry::instanceExists(\OxidEsales\Eshop\Core\Session::class)) {
            $return = (string) Registry::getSession()->getVariable('auth');
        }

        return $return;
    }
}
