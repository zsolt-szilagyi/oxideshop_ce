<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

/**
 * Class for handling database related operations
 *
 */
class oxDbMetaDataHandler extends oxSuperCfg
{
    const MULTILANG_TABLE_POSTFIX = '_multilang';

    /**
     *
     * @var array
     */
    protected $_aDbTablesFields = null;

    /**
     *
     * @var array
     */
    protected $_aTables = null;

    /**
     *
     * @var int
     */
    protected $_iCurrentMaxLangId;

    /**
     *
     * @var array Tables which should be skipped from resetting
     */
    protected $_aSkipTablesOnReset = array("oxcountry");

    /**
     * When creating views, always use those fields from core table.
     *
     * @var array
     */
    protected $forceOriginalFields = array('OXID');

    /**
     * Fields in *_multilang that are not for multilanguage content.
     * @var array
     */
    protected $noMultilanguageFields = array('OXID', 'OXLANG', 'OXTIMESTAMP');

    /**
     *  Get table fields
     *
     * @param string $sTableName  table name
     *
     * @return array
     */
    public function getFields($sTableName)
    {
        $aFields = array();
        $aRawFields = oxDb::getDb()->MetaColumns($sTableName);
        if (is_array($aRawFields)) {
            foreach ($aRawFields as $oField) {
                $aFields[$oField->name] = "{$sTableName}.{$oField->name}";
            }
        }

        return $aFields;
    }

    /**
     * Check if table exists
     *
     * @param string $sTableName table name
     *
     * @return bool
     */
    public function tableExists($sTableName)
    {
        $oDb = oxDb::getDb();
        $aTables = $oDb->getAll("show tables like " . $oDb->quote($sTableName));

        return count($aTables) > 0;
    }

    /**
     * Check if field exists in table
     *
     * @param string $sFieldName field name
     * @param string $sTableName table name
     *
     * @return bool
     */
    public function fieldExists($sFieldName, $sTableName)
    {
        $aTableFields = $this->getFields($sTableName);
        $sTableName = strtoupper($sTableName);
        if (is_array($aTableFields)) {
            $sFieldName = strtoupper($sFieldName);
            $aTableFields = array_map('strtoupper', $aTableFields);
            if (in_array("{$sTableName}.{$sFieldName}", $aTableFields)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get all tables names from db. Views tables are not included in
     * this list.
     *
     * @return array
     */
    public function getAllTables()
    {
        if (empty($this->_aTables)) {
            $aTables = oxDb::getDb()->getAll("show tables");

            foreach ($aTables as $aTableInfo) {
                if ($this->validateTableName($aTableInfo[0])) {
                    $this->_aTables[] = $aTableInfo[0];
                }
            }
        }

        return $this->_aTables;
    }

    /**
     * Get extension table name containing the multilanguage data.
     *
     * @param $table
     *
     * @return string
     */
    public function getLanguageExtensionTableName($table)
    {
        $result = $this->getCoreTableName($table) . self::MULTILANG_TABLE_POSTFIX;
        return $result;
    }

    /**
     * return all DB tables for the language sets
     *
     * @param string $table table name to check
     *
     * @return array
     */
    public function getAllMultiTables($table)
    {
        $result = array();
        $multilanguageTable = $this->getLanguageExtensionTableName($table);
        if ($this->tableExists($multilanguageTable)) {
            $result[] = $multilanguageTable;
        }
        return $result;
    }

    /**
     * Get table multi-language fields.
     *
     * @param string $sTable table name
     *
     * @return array
     */
    public function getMultilangFields($table)
    {
        $result = array();
        $multilanguageTable = $this->getLanguageExtensionTableName($table);
        if ($this->tableExists($multilanguageTable)) {
            $result = $this->getFields($multilanguageTable);
            foreach ($this->noMultilanguageFields as $field)
            {
                if (array_key_exists($field, $result)) {
                    unset($result[$field]);
                }
            }
        }
        $result = array_keys($result);
        return $result;
    }

    /**
     * Get unpostfixed fields to postfixed fields relation.
     *
     * @param $table
     *
     * @return array
     */
    public function getSinglelangFields($table, $languageId)
    {
        $table = strtolower($table);
        $fields = array();
        $languageTable = $table;

        //core table handling
        if (false === strpos($table, 'oxv_')) {
            $languageTable = $this->getLanguageExtensionTableName($table);
            $baseFields = $this->getFields($table);
            $languageFields = $this->getFields($languageTable);
            $fields = array_merge($baseFields, $languageFields);
        } else {
            $fields = $this->getFields($table);
        }

        $singleLanguageFields = array();

        foreach ($fields as $fieldName => $field) {
            if (preg_match("/(({$table}|{$languageTable})\.)?(?<field>[^_]+)_(?<lang>[a-zA-Z0-9_]+)$/", $field, $matches)) {
                if (ltrim($matches['lang'], '_') == strtoupper($languageId)) {
                    $singleLanguageFields[$matches['field']] = $field;
                }
            } else {
                $tmp = explode('.', $field);
                if (false === strpos($tmp[1], '_')) {
                    $singleLanguageFields[$fieldName] = $field;
                }
            }
        }

        return $singleLanguageFields;
    }

    /**
     * Resetting all multi-language fields with specific language id
     * to default value in selected table
     *
     * @param string $languageId
     * @param string $tableName
     *
     * @return null
     */
    public function resetMultilangFields($languageId, $tableName)
    {
        $languageTable = $this->getLanguageExtensionTableName($tableName);

        $query = "DELETE FROM {$languageTable} WHERE OXLANG = '{$languageId}'";
        $this->executeSql($query);
    }

    /**
     * Add new language to database. Scans all tables and adds new
     * multi-language fields
     *
     * @param $languageId Language id/abbreviation to add.
     */
    public function addNewLanguageToDb($languageId)
    {
        //when adding a multilanguage table, related <table>_multilang table needs
        //to be created as well, this needs to be done manually for now
        $this->safeGuardAdditionalMultiLanguageTables();

        //updating views
        $this->updateViews();
    }

    /**
     * Resetting all multi-language fields with specific language id
     * to default value in all tables. Only if language ID > 0.
     *
     * @param int $iLangId Language id
     *
     * @return null
     */
    public function resetLanguage($iLangId)
    {
        if (empty($iLangId)) {
            return;
        }

        $aTables = $this->getAllTables();

        // removing tables which does not requires reset
        foreach ($this->_aSkipTablesOnReset as $sSkipTable) {

            if (($iSkipId = array_search($sSkipTable, $aTables)) !== false) {
                unset($aTables[$iSkipId]);
            }
        }

        foreach ($aTables as $sTableName) {
            $this->resetMultilangFields($iLangId, $sTableName);
        }
    }

    /**
     * Executes array of sql strings
     *
     * @param array $aSql SQL query array
     */
    public function executeSql($aSql)
    {
        $oDb = oxDb::getDb();

        if (is_array($aSql) && !empty($aSql)) {
            foreach ($aSql as $sSql) {
                $sSql = trim($sSql);
                if (!empty($sSql)) {
                    $oDb->execute($sSql);
                }
            }
        }
    }

    /**
     * Updates all views
     *
     * @param array $aTables array of DB table name that can store different data per shop like oxArticle
     *
     * @return bool
     */
    public function updateViews($aTables = null)
    {
        set_time_limit(0);

        $oDb = oxDb::getDb();
        $oConfig = oxRegistry::getConfig();

        $this->safeGuardAdditionalMultiLanguageTables();

        $aShops = $oDb->getAll("select * from oxshops");

        $aTables = $aTables ? $aTables : $oConfig->getConfigParam('aMultiShopTables');

        $bSuccess = true;
        foreach ($aShops as $aShop) {
            $sShopId = $aShop[0];
            $oShop = oxNew('oxShop');
            $oShop->load($sShopId);
            $oShop->setMultiShopTables($aTables);
            $aMallInherit = array();
            foreach ($aTables as $sTable) {
                $aMallInherit[$sTable] = $oConfig->getShopConfVar('blMallInherit_' . $sTable, $sShopId);
            }
            if (!$oShop->generateViews(false, $aMallInherit) && $bSuccess) {
                $bSuccess = false;
            }
        }

        return $bSuccess;
    }

    /**
     * Ensure that all *_set* tables for all tables in config parameter 'aMultiLangTables'
     * are created.
     *
     * @return null
     */
    protected function safeGuardAdditionalMultiLanguageTables()
    {
        $multiLanguageTables = $this->getConfig()->getConfigParam('aMultiLangTables');
        if (!is_array($multiLanguageTables) || empty($multiLanguageTables)) {
            return; //nothing to do
        }

        foreach ($multiLanguageTables as $table) {
            $extensionTable = $this->getLanguageExtensionTableName($table);
            if (!$this->tableExists($extensionTable)) {
                throw new oxException('Missing table ' . $extensionTable);
            }
        }
    }

    /**
     * Mark view tables as invalid.
     *
     * @param string $tableName
     *
     * @return bool
     */
    protected function validateTableName($tableName)
    {
        $result = strpos($tableName, "oxv_") !== 0;
        return $result;
    }

    /**
     * Get core table name from table name
     *
     * @param $tableName
     */
    protected function getCoreTableName($tableName)
    {
        //TODO: use preg_match instead of explode
        $tmp = explode('oxv_', $tableName);
        $raw = $tmp[count($tmp)-1];
        $tmp = explode('_',$raw);

        return $tmp[0];
    }

    public function getTableSetForLanguageAbbreviation($languageAbbreviation, $table = 'oxarticles')
    {
        $coreTableName = $this->getCoreTableName($table);
        $return = $this->getLanguageExtensionTableName($coreTableName);
        return $return;
    }
}
