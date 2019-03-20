<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;


use OxidEsales\EshopCommunity\Internal\Templating\TemplateRenderer;

class SmartyTemplateRendererBridge
{
    private $engine;

    /**
     * @var SmartyFactoryInterface
     */
    private $factory;

    public function __construct(SmartyFactoryInterface $engineFactory)
    {
        $this->factory = $engineFactory;
    }

    public function getTemplateRenderer()
    {
        return new TemplateRenderer($this->getTemplateEngine());
    }

    public function setEngine($engine)
    {
        $this->factory->setSmarty($engine);
    }

    public function getTemplateEngine()
    {
        return new SmartyEngine($this->factory->getSmarty());
    }

    public function getEngine()
    {
        $this->engine;
    }
}