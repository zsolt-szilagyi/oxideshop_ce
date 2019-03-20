<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

/**
 * Interface TemplateRendererInterface
 */
interface TemplateRendererInterface
{
    /**
     * @param string $template The template name
     * @param array  $viewData An array of parameters to pass to the template
     * @param string $cacheId  The id for template caching
     *
     * @return string
     */
    public function renderTemplate(string $template, array $viewData = [], $cacheId = null): string;

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment The template fragment to render
     * @param array  $viewData An array of parameters to pass to the template
     * @param string $cacheId  The id for template caching
     *
     * @return string
     */
    public function renderFragment(string $template, array $viewData = [], $cacheId = null) : string;

    /**
     * @return TemplateEngineInterface
     */
    public function getEngine();

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists($name);
}
