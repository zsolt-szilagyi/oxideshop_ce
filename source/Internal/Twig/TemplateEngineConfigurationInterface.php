<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig;

/**
 * Interface TemplateEngineConfigurationInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Twig
 */
interface TemplateEngineConfigurationInterface
{

    /**
     * @return array
     */
    public function getParameters(): array;
}
