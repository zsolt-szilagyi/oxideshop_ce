<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContext;

/**
 * Class SmartyContext
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyContext extends BasicContext implements SmartyContextInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UtilsView
     */
    private $utilsView;

    /**
     * Context constructor.
     *
     * @param Config    $config
     * @param UtilsView $utilsView
     */
    public function __construct(Config $config, UtilsView $utilsView)
    {
        $this->config = $config;
        $this->utilsView = $utilsView;
    }

    /**
     * @return bool
     */
    public function getTemplateEngineDebugMode(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode == 1 || $debugMode == 3 || $debugMode == 4);
    }

    /**
     * @return bool
     */
    public function showTemplateNames(): bool
    {
        $debugMode = $this->getConfigParameter('iDebug');
        return ($debugMode == 8 && !$this->getBackendMode());
    }

    /**
     * @return bool
     */
    public function getTemplateSecurityMode(): bool
    {
        return (bool) $this->getDemoShopMode();
    }

    /**
     * @return string
     */
    public function getShopCompileDirectory(): string
    {
        return (string) $this->getConfigParameter('sCompileDir');
    }

    /**
     * @return array
     */
    public function getTemplateDirectories(): array
    {
        return $this->utilsView->getTemplateDirs();
    }

    /**
     * @return bool
     */
    public function getTemplateCompileCheckMode(): bool
    {
        return (bool) $this->getConfigParameter('blCheckTemplates');
    }

    /**
     * @return bool
     */
    public function getTemplatePhpHandlingMode(): bool
    {
        return (bool) $this->getConfigParameter('iSmartyPhpHandling');
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getTemplatePath($templateName): string
    {
        return $this->config->getTemplatePath($templateName, $this->getBackendMode());
    }

    /**
     * @return int
     */
    public function getCurrentShopId(): int
    {
        return $this->config->getShopId();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    private function getConfigParameter(string $name)
    {
        return $this->config->getConfigParam($name);
    }

    /**
     * @return bool
     */
    private function getBackendMode(): bool
    {
        return (bool) $this->config->isAdmin();
    }

    /**
     * @return bool
     */
    private function getDemoShopMode(): bool
    {
        return (bool) $this->config->isDemoShop();
    }
}
