<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Class SmartyEngineConfiguration
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
interface SmartyEngineConfigurationInterface
{
    const BASIC_SETTINGS = 'settings';

    const SECURITY_SETTINGS = 'security_settings';

    const PLUGINS = 'plugins';

    const PREFILTER = 'prefilters';

    const RESOURCES = 'resources';

    /**
     * @param string $type
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function getConfiguration(string $type, $default = null);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasConfiguration(string $type): bool;
}
