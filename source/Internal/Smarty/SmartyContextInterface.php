<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;

/**
 * Interface SmartyContextInterface
 */
interface SmartyContextInterface extends BasicContextInterface
{
    /**
     * @return bool
     */
    public function getTemplateEngineDebugMode(): bool;

    /**
     * @return bool
     */
    public function showTemplateNames(): bool;

    /**
     * @return bool
     */
    public function getTemplateSecurityMode(): bool;

    /**
     * @return string
     */
    public function getShopCompileDirectory(): string;

    /**
     * @return array
     */
    public function getTemplateDirectories(): array;

    /**
     * @return bool
     */
    public function getTemplateCompileCheckMode(): bool;

    /**
     * @return bool
     */
    public function getTemplatePhpHandlingMode(): bool;

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getTemplatePath($templateName): string;

    /**
     * @return int
     */
    public function getCurrentShopId(): int;
}
