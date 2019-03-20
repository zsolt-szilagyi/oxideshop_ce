<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;


interface TemplateCompilePathProviderInterface
{
    /**
     * Returns a full path to engine compile dir
     *
     * @return string
     */
    public function getTemplateCompilePath(): string;
}