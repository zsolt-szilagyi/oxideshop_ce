<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig;

/**
 * Interface TwigContextInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Twig
 */
interface TwigContextInterface
{
    /**
     * @return array
     */
    public function getTemplateDirectories(): array;

    /**
     * @return boolean
     */
    public function getIsDebug(): bool;

    /**
     * @return string
     */
    public function getCacheDir(): string;
}
