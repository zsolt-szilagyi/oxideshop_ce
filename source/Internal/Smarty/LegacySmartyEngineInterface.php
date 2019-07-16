<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Class LegacySmartyEngineInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
interface LegacySmartyEngineInterface
{
    /**
     * @return \Smarty
     */
    public function getSmarty();

    /**
     * @param \Smarty $smarty
     */
    public function setSmarty($smarty);
}
