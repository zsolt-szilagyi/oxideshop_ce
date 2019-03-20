<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use Psr\Container\ContainerInterface;
use OxidEsales\EshopCommunity\Internal\Templating\TemplateRendererInterface;

/**
 * Smarty renderer class
 * Renders smarty template with given parameters and returns rendered body.
 *
 * @deprecated since v6.4 (2019-03-19); This class will be removed,
 * please use OxidEsales\EshopCommunity\Internal\Templating\TemplateRendererInterface instead.
 */
class SmartyRenderer
{
    /**
     * Template renderer
     *
     * @param string $sTemplateName Template name.
     * @param array  $aViewData     Array of view data (optional).
     *
     * @return string
     */
    public function renderTemplate($sTemplateName, $aViewData = [])
    {
        /* $oSmarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();*/
        return $this->getContainer()
            ->get(TemplateRendererInterface::class)
            ->renderTemplate($sTemplateName, $aViewData);
    }

    /**
     * @internal
     *
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return \OxidEsales\EshopCommunity\Internal\Application\ContainerFactory::getInstance()->getContainer();
    }
}
