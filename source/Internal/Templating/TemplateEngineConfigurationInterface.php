<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

/**
 * Interface TemplateEngineConfigurationInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Templating
 */
interface TemplateEngineConfigurationInterface
{
    /**
     * @return array
     */
    public function getParameters();
}
