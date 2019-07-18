<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig;

/**
 * Interface TwigEngineConfigurationInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Twig
 */
interface TwigEngineConfigurationInterface
{
    /**
     * @return array
     */
    public function getParameters(): array;
}
