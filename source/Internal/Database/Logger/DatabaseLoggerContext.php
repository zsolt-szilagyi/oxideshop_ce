<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Database\Logger;

use Psr\Log\LogLevel;
use Webmozart\PathUtil\Path;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Facts\Config\ConfigFile as FactsConfigFile;

/**
 * @internal
 */
class DatabaseLoggerContext implements DatabaseLoggerContextInterface
{
    const ENABLE_ADMIN_LOG_CONFIGFILE_VARNAME = 'blLogChangesInAdmin';

    const SKIP_LOG_TAGS_CONFIG_VARNAME = 'aLogSkipTags';

    const ADMIN_LOGIN_SESSION_VARNAME = 'auth';

    const ADMIN_LOGGER_PREFIX = 'AdminQueryLog';

    /**
     * @var FactsConfigFile
     */
    private $factsConfigFile;

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return isAdmin();
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return Registry::getConfig()->getShopId();
    }

    /**
     * @return string
     */
    public function getLogLevel(): string
    {
        return LogLevel::DEBUG;
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        $shopPath = $this->getFromFactsConfigFile('sShopDir') ;

        return Path::join($shopPath, 'log', 'oxadmin.log');
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
            $skipLogTags = Registry::getConfig()->getConfigParam(self::SKIP_LOG_TAGS_CONFIG_VARNAME);
        }

        return (array) $skipLogTags;
    }

    /**
     * @return bool
     */
    public function isEnabledAdminQueryLog(): bool
    {
        return $this->getFromFactsConfigFile(self:: ENABLE_ADMIN_LOG_CONFIGFILE_VARNAME);
    }

    /**
     * @return string
     */
    public function getAdminLogPrefix(): string
    {
        return self::ADMIN_LOGGER_PREFIX;
    }

    /**
     * Getter for user id
     *
     * @return string
     */
    public function getUserId(): string
    {
        return (string) Registry::getSession()->getVariable(self::ADMIN_LOGIN_SESSION_VARNAME);
    }

    /**
     * @param string $varName
     *
     * @return string
     */
    private function getFromFactsConfigFile(string $varName): string
    {
        if (!is_a($this->factsConfigFile, FactsConfigFile::class)) {
            $this->factsConfigFile = new FactsConfigFile();
        }

        return $this->factsConfigFile->getVar($varName);
    }
}
