<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

/**
 * Class DelegatingEngine
 */
class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $engine;

    /**
     * @param TemplateEngineInterface $engine
     */
    public function __construct(TemplateEngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @param string $template The template name
     * @param array  $viewData An array of parameters to pass to the template
     * @param string $cacheId  The id for template caching
     *
     * @return string
     */
    public function renderTemplate(string $template, array $viewData = [], $cacheId = null) : string
    {
        /** @var TemplateEngineInterface $templating */
        $templating = $this->getEngine();
        $templating->setCacheId($cacheId);

        return $templating->render($template, $viewData);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment The template fragment to render
     * @param array  $viewData An array of parameters to pass to the template
     * @param string $cacheId  The id for template caching
     *
     * @return string
     */
    public function renderFragment(string $fragment, array $viewData = [], $cacheId = null) : string
    {
        /** @var TemplateEngineInterface $templating */
        $templating = $this->getEngine();
        $templating->setCacheId($cacheId);

        return $templating->renderFragment($fragment, $viewData);
    }

    /**
     * Return fallback engine.
     *
     * @return TemplateEngineInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists($name) : bool
    {
        return $this->getEngine()->exists($name);
    }
}
