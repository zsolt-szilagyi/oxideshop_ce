<?php

namespace OxidEsales\Eshop\Core\Modules;

use OxidEsales\Eshop\Core\UtilsObject;

/**
 * Class ModulesInstallerHelper
 * @package OxidEsales\Eshop\Core\Modules
 */
class ModulesInstallerHelper
{
    /**
     * Parse values
     *
     * @return array
     */
    public static function parseTemplateBlockValues()
    {
        $id = utilsObject::getInstance()->generateUId();

        $result = [
            'id' => $id
        ];

        return $result;
    }
}
