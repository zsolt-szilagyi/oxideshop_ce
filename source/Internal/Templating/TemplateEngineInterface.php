<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

/**
 * Interface EngineInterface
 */
interface TemplateEngineInterface
{
    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal($name, $value);

    /**
     * Returns assigned globals.
     *
     * @return array
     */
    public function getGlobals();

    /**
     * @param string $cacheId
     */
    public function setCacheId($cacheId);

    /**
     * Returns the template file extension.
     *
     * @return string
     */
    public function getDefaultFileExtension();

    /**
     * Renders a template.
     *
     * @param string $name       A template name
     * @param array  $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function render(string $name, array $parameters = []);

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param array  $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function renderFragment(string $fragment, array $parameters = []);

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists($name);
}
