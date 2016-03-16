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
 * Shop manager.
 * Performs configuration and object loading or deletion.
 */
class oxShop extends oxI18n
{
    /** @var string Name of current class. */
    protected $_sClassName = 'oxshop';

    /** @var array Multi shop tables, set in config. */
    protected $_aMultiShopTables = null;

    /** @var array Query variables. */
    protected $_aQueries = array();

    /** @var array Database tables. */
    protected $_aTables = null;

    /** @var bool Defines if multishop inherits categories. */
    protected $_blMultiShopInheritCategories = false;

    /**
     * Fields that are used in *_multilang table but should show up in the views.
     *
     * @var array
     */
    protected $skipExtensionTableFields = array('OXLANG');

    /**
     * Database tables setter.
     *
     * @param array $aTables
     */
    public function setTables($aTables)
    {
        $this->_aTables = $aTables;
    }

    /**
     * Database tables getter.
     *
     * @return array
     */
    public function getTables()
    {
        if (is_null($this->_aTables)) {
            $aTables = $this->formDatabaseTablesArray();
            $this->setTables($aTables);
        }

        return $this->_aTables;
    }

    /**
     * Database queries setter.
     *
     * @param array $aQueries
     */
    public function setQueries($aQueries)
    {
        $this->_aQueries = $aQueries;
    }

    /**
     * Database queries getter.
     *
     * @return array
     */
    public function getQueries()
    {
        return $this->_aQueries;
    }

    /**
     * Add a query to query array.
     *
     * @param string $sQuery
     */
    public function addQuery($sQuery)
    {
        $this->_aQueries[] = $sQuery;
    }

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();

        if (!$this->isShopValid()) {
            return;
        }

        $this->init('oxshops');

        if ($iMax = $this->getConfig()->getConfigParam('iMaxShopId')) {
            $this->setMaxShopId($iMax);
        }
    }

    /**
     * Sets multi shop tables
     *
     * @param string $aMultiShopTables multi shop tables
     */
    public function setMultiShopTables($aMultiShopTables)
    {
        $this->_aMultiShopTables = $aMultiShopTables;
    }

    /**
     * Get multishop table array
     *
     * @return array
     */
    public function getMultiShopTables()
    {
        if (is_null($this->_aMultiShopTables)) {
            $this->_aMultiShopTables = array();
        }

        return $this->_aMultiShopTables;
    }

    /**
     * (Re)generates shop views
     *
     * @param bool  $multishopInheritCategories Config option blMultishopInherit_oxcategories
     * @param array $mallInherit                Array of config options blMallInherit
     *
     * @return bool is all views generated successfully
     */
    public function generateViews($multishopInheritCategories = false, $mallInherit = null)
    {
        $this->_prepareViewsQueries();
        $blSuccess = $this->_runQueries();

        $this->_cleanInvalidViews();

        return $blSuccess;
    }

    /**
     * Returns default category of the shop.
     *
     * @return string
     */
    public function getDefaultCategory()
    {
        return $this->oxshops__oxdefcat->value;
    }

    /**
     * Returns true if shop in productive mode
     *
     * @return bool
     */
    public function isProductiveMode()
    {
        return (bool) $this->oxshops__oxproductive->value;
    }

    /**
     * Creates view query and adds it to query array.
     *
     * @param string $sTable     Table name
     * @param array  $aLanguages Language array( id => abbreviation )
     */
    public function createViewQuery($sTable, $aLanguages = null)
    {
        $sStart = 'CREATE OR REPLACE SQL SECURITY INVOKER VIEW';

        if (!is_array($aLanguages)) {
            $aLanguages = array(null => null);
        }

        foreach ($aLanguages as $iLang => $sLang) {
            $this->addViewLanguageQuery($sStart, $sTable, $iLang, $sLang);
        }
    }

    /**
     * Returns table field name mapping sql section for single language views
     *
     * @param string $table     Table name
     *
     * @return string
     */
    protected function _getSingleLanguageViewSelect($table)
    {
        $fields = array();
        $metaData = oxNew('oxDbMetaDataHandler');
        $extensionTable = $metaData->getLanguageExtensionTableName($table);
        $tables = array($table);
        if ($metaData->tableExists($extensionTable)) {
            $tables[] = $extensionTable;
        }

        foreach ($tables as $tableKey => $tableName) {
            $tableFields = $metaData->getFields($tableName);
            foreach ($tableFields as $coreField => $field) {
                $skipField = (0 < $tableKey) && in_array($coreField, $this->skipExtensionTableFields);
                if (!isset($fields[$coreField]) && !$skipField) {
                    $fields[$coreField] = $field;
                }
            }
        }

        return implode(',', $fields);
    }

    /**
     * Returns table fields sql section for multiple language views
     *
     * @param string $table table name
     *
     * @return string
     */
    protected function _getViewSelectAll($table)
    {
        $metaData = oxNew('oxDbMetaDataHandler');
        $extensionTable = $metaData->getLanguageExtensionTableName($table);
        $fields = $metaData->getFields($table);
        $extensionFields = $metaData->tableExists($extensionTable) ? $metaData->getFields($extensionTable) : array();

        $languageIds = oxRegistry::getLang()->getLanguageIds($this->getId());
        foreach ($languageIds as $languageId) {
            foreach ($extensionFields as $coreField => $field) {
                $skipField = in_array($coreField, $this->skipExtensionTableFields);
                $ucLanguageId = strtoupper($languageId);
                if (!isset($fields[$coreField]) && !$skipField) {
                    $fields[$coreField . '_' . $languageId] = "mlang_{$languageId}.{$coreField} AS {$coreField}_{$ucLanguageId}";
                }
            }

        }
        return implode(',', $fields);
    }

    /**
     * Returns all language table view JOIN section
     *
     * @param string $sTable table name
     *
     * @return string $sSQL
     */
    protected function _getViewJoinAll($sTable)
    {
        $join = ' ';
        $metaDataHandler = oxNew('oxDbMetaDataHandler');
        $extensionTable = $metaDataHandler->getLanguageExtensionTableName($sTable);
        if ($metaDataHandler->tableExists($extensionTable)) {
            $languageIds = oxRegistry::getLang()->getLanguageIds($this->getId());
            foreach ($languageIds as $languageId) {
                $alias = 'mlang_' . $languageId;
                $join .= "LEFT JOIN {$extensionTable} AS {$alias} ON ({$sTable}.OXID = {$alias}.OXOBJECTID AND {$alias}.OXLANG = '{$languageId}') ";
            }
        }
        return $join;
    }

    /**
     * Returns language table view JOIN section
     *
     * @param string $table table name
     * @param string $languageId language id
     *
     * @return string $sSQL
     */
    protected function _getViewJoinLang($table, $languageId)
    {
        $join = ' ';
        $metaDataHandler = oxNew('oxDbMetaDataHandler');
        $extensionTable = $metaDataHandler->getLanguageExtensionTableName($table);

        if ($metaDataHandler->tableExists($extensionTable)) {
            $join .= "LEFT JOIN {$extensionTable} ON ({$table}.OXID = {$extensionTable}.OXOBJECTID AND {$extensionTable}.OXLANG = '{$languageId}') ";
        }

        return $join;
    }

    /**
     * Gets all invalid views and drops them from database
     */
    protected function _cleanInvalidViews()
    {
        $oDb = oxDb::getDb();
        $oLang = oxRegistry::getLang();
        $aLanguages = $oLang->getLanguageIds($this->getId());

        $aMultilangTables = oxRegistry::getLang()->getMultiLangTables();
        $aMultishopTables = $this->getMultiShopTables();

        $oLang = oxRegistry::getLang();
        $aAllShopLanguages = $oLang->getAllShopLanguageIds();

        $oViewsValidator = oxNew('oxShopViewValidator');

        $oViewsValidator->setShopId($this->getId());
        $oViewsValidator->setLanguages($aLanguages);
        $oViewsValidator->setAllShopLanguages($aAllShopLanguages);
        $oViewsValidator->setMultiLangTables($aMultilangTables);
        $oViewsValidator->setMultiShopTables($aMultishopTables);

        $aViews = $oViewsValidator->getInvalidViews();

        foreach ($aViews as $sView) {
            $oDb->execute('DROP VIEW IF EXISTS ' . $sView);
        }
    }

    /**
     * Creates all view queries and adds them in query array
     */
    protected function _prepareViewsQueries()
    {
        $oLang = oxRegistry::getLang();
        $aLanguages = $oLang->getLanguageIds($this->getId());

        $aMultilangTables = oxRegistry::getLang()->getMultiLangTables();
        $aTables = $this->getTables();
        foreach ($aTables as $sTable) {
            $this->createViewQuery($sTable);
            if (in_array($sTable, $aMultilangTables)) {
                $this->createViewQuery($sTable, $aLanguages);
            }
        }
    }

    /**
     * Adds view language query to query array.
     *
     * @param string $queryStart
     * @param string $table
     * @param int    $languageId
     * @param string $languageAbbr
     */
    protected function addViewLanguageQuery($queryStart, $table, $languageId, $languageAbbr)
    {
        $sLangAddition = $languageAbbr === null ? '' : "_{$languageAbbr}";

        $sViewTable = "oxv_{$table}{$sLangAddition}";

        if ($languageAbbr === null) {
            $sFields = $this->_getViewSelectAll($table);
            $sJoin = $this->_getViewJoinAll($table);
        } else {
            $sFields = $this->_getSingleLanguageViewSelect($table);
            $sJoin = $this->_getViewJoinLang($table, $languageId);
        }

        $sQuery = "{$queryStart} `{$sViewTable}` AS SELECT {$sFields} FROM {$table}{$sJoin}";
        $this->addQuery($sQuery);
    }

    /**
     * Runs stored queries
     * Returns false when any of the queries fail, otherwise return true
     *
     * @return bool
     */
    protected function _runQueries()
    {
        $oDb = oxDb::getDb();
        $aQueries = $this->getQueries();
        $bSuccess = true;
        foreach ($aQueries as $sQuery) {
            if (!$oDb->execute($sQuery)) {
                $bSuccess = false;
            }
        }

        return $bSuccess;
    }

    /**
     * Forms array of tables which are available.
     *
     * @return array
     */
    protected function formDatabaseTablesArray()
    {
        $multilanguageTables = oxRegistry::getLang()->getMultiLangTables();

        return array_unique($multilanguageTables);
    }

    /**
     * Checks whether current shop is valid.
     *
     * @return bool
     */
    protected function isShopValid()
    {
        return true;
    }
}
