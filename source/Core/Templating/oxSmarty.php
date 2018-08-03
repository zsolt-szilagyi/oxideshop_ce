<?php
/**
 * Created by PhpStorm.
 * User: vilma
 * Date: 03.08.18
 * Time: 11:53
 */

namespace OxidEsales\EshopCommunity\Core\Templating;


use OxidEsales\EshopCommunity\Core\Registry;

class oxSmarty
{

    /**
     * Sets template content from cache. In demoshop enables security mode.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName    name of template
     * @param string &$sTplSource Template source
     * @param object $oSmarty     not used here
     *
     * @return bool
     */
    public static function ox_get_template($sTplName, &$sTplSource, $oSmarty)
    {
        $sTplSource = $oSmarty->oxidcache->value;
        if (Registry::getConfig()->isDemoShop()) {
            $oSmarty->security = true;
        }

        return true;
    }

    /**
     * Sets time for smarty templates recompilation. If oxidtimecache is set, smarty will cache templates for this period.
     * Otherwise templates will always be compiled.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName       name of template
     * @param string &$iTplTimestamp template timestamp referense
     * @param object $oSmarty        not used here
     *
     * @return bool
     */
    public static function ox_get_timestamp($sTplName, &$iTplTimestamp, $oSmarty)
    {
        $iTplTimestamp = isset($oSmarty->oxidtimecache->value) ? $oSmarty->oxidtimecache->value : time();

        return true;
    }

    /**
     * Dummy function, required for smarty plugin registration.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName not used here
     * @param object $oSmarty  not used here
     *
     * @return bool
     */
    public static function ox_get_secure($sTplName, $oSmarty)
    {
        return true;
    }

    /**
     * Dummy function, required for smarty plugin registration.
     *
     * @see http://www.smarty.net/docsv2/en/template.resources.tpl
     *
     * @param string $sTplName not used here
     * @param object $oSmarty  not used here
     */
    public static function ox_get_trusted($sTplName, $oSmarty)
    {
    }
}