<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\EshopCommunity\Internal\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Templating\TemplateRendererInterface;

class SmartyTemplateRendererBridge implements TemplateRendererBridgeInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * SmartyTemplateRendererBridge constructor.
     *
     * @param TemplateRendererInterface $renderer
     */
    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return TemplateRendererInterface
     */
    public function getTemplateRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param mixed $engine
     */
    public function setEngine($engine)
    {
        $this->renderer->getTemplateEngine()->setSmarty($engine);
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->renderer->getTemplateEngine()->getSmarty();
    }
}
