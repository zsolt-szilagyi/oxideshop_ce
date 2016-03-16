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

use OxidEsales\TestingLibrary\UnitTestCase;

require_once realpath(dirname(__FILE__)) . '/helpers/LanguageMainHelper.php';

class Integration_Multilanguage_RestructuredMultilanguageTablesTest extends UnitTestCase
{
    protected $shopId = 'oxbaseshop';

    /**
     * Original tables and fields.
     *
     * @var array
     */
    protected $originalTables = array();
    protected $originalFields = array();

    /**
     * Fixture setUp
     */
    protected function setUp()
    {
        parent::setUp();

        if ('EE' == $this->getTestConfig()->getShopEdition() ) {
            $this->shopId = '1';
            $this->setConfigParam('blAllowSharedEdit', 1);
        }

    }

    /**
     * Set up before running test suite.
     */
    public function setUpBeforeTestSuite()
    {
        parent::setUpBeforeTestSuite();

        $this->prepareDatabase();
        $this->restructureDatabase();
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass()
    {
        $serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();

        if ('CE' == oxRegistry::getConfig()->getConfigParam('edition')) {
            $serviceCaller->setParameter('importSql', '@'. getShopBasePath() .'/Setup/Sql/database.sql');
        }
        if ('PE' == oxRegistry::getConfig()->getConfigParam('edition')) {
            $serviceCaller->setParameter('importSql', '@'. getShopBasePath() .'/Edition/Professional/Setup/Sql/database.sql');
        }

        if ('EE' == oxRegistry::getConfig()->getConfigParam('edition')) {
            $serviceCaller->setParameter('importSql', '@'. getShopBasePath() .'/Edition/Enterprise/Setup/Sql/database.sql');
        }

        $serviceCaller->callService('ShopPreparation', 1);

        parent::tearDownAfterClass();
    }

    /**
     * Data provider to run new structure test with or without updateView call.
     *
     * @return array
     */
    public function providerTestNewStructure()
    {
        $data = array( array(false), array(true) );
        return $data;
    }

    /**
     * Test database installation after restructuring.
     *
     * @dataProvider providerTestNewStructure
     *
     * @param bool $updateViews
     */
    public function testNewStructure($updateViews)
    {
        $postfixes = array('', '_de', '_en');

        $multilanguageTables = oxRegistry::getLang()->getMultiLangTables();
        $metaDataHandler = oxNew('oxDbMetaDataHandler');
        if ($updateViews) {
            $metaDataHandler->updateViews();
        }

        $baseQuery = "SELECT count(*) from ";

        foreach ($multilanguageTables as $table) {
            $counted = oxDB::getDb()->getOne($baseQuery . $table);
            foreach ($postfixes as $postfix) {
                $view = 'oxv_' . $table . $postfix;
                $this->assertTrue($metaDataHandler->tableExists($view));
                $viewCounted = oxDB::getDb()->getOne($baseQuery . $view);
                $this->assertSame($counted, $viewCounted, 'incorrect content for ' . $view);
            }
        }
    }

    /**
     * Test loading category list.
     */
    public function testLoadCategoryList()
    {
        $categoryList = oxNew('oxCategoryList');
        $categoryList->loadList();

        foreach ($categoryList->aList as $listItem) {
            $this->assertFalse(0 == strlen($listItem->oxcategories__oxtitle->getRawValue()));
        }
    }

    /**
     * Data provider to run new structure test with or without updateView call.
     *
     * @return array
     */
    public function providerAdminMode()
    {
        $data = array(array(false), array(true));
        return $data;
    }

    /**
     * Test load a category.
     *
     * @dataProvider providerAdminMode
     *
     * @param bool $adminMode
     */
    public function testLoadCategory($adminMode)
    {
        $this->setAdminMode($adminMode);
        $categoryId = '943a9ba3050e78b443c16e043ae60ef3';
        $category = oxNew('oxCategory');
        $viewTable = $this->getViewTable('oxcategories', 'de');

        $query = $category->buildSelectString(array("`{$viewTable}`.`oxid`" => $categoryId));
        $this->assertContains("`{$viewTable}`.`oxparentid`", $query);
        $this->assertContains("`{$viewTable}`.`oxtitle`", $query);

        $category->load($categoryId);
        $this->assertSame('Eco-Fashion', $category->oxcategories__oxtitle->value);
    }

    /**
     * Test load an article.
     *
     * @dataProvider providerAdminMode
     *
     * @param bool $adminMode
     */
    public function testLoadArticle($adminMode)
    {
        $this->setAdminMode($adminMode);
        $article = oxNew('oxArticle');
        $article->load('1126');

        $this->assertSame('Bar-Set ABSINTH', $article->oxarticles__oxtitle->value);
        $longDescription = $article->getLongDescription();
        $this->assertContains('von Hemingway bis Oscar Wild', $longDescription->value);

    }

    /**
     * Test multilingual field status.
     */
    public function testShopMultilingualFieldStatus()
    {
        $shop = $this->getProxyClass('oxShop');
        $this->assertEquals('oxv_oxshops_de', $shop->getViewName());
        $this->assertSame(0, $shop->_getFieldStatus('oxcountry'));
        $this->assertSame(1, $shop->_getFieldStatus('oxregistersubject'));
        $this->assertSame(1, $shop->_getFieldStatus('oxregistersubject_de'));
    }

    /**
     * Test loading from main view table.
     */
    public function testBaseLoadFromView()
    {
        $base = oxNew('oxbase');
        $base->init('oxv_oxshops');
        $this->assertContains('oxordersubject_de', $base->getSelectFields());

        $base->load($this->shopId);
        $this->assertSame('Ihre Bestellung bei OXID eSales', $base->oxv_oxshops__oxordersubject_de->value);
    }

    /**
     * Test loading from main view table.
     */
    public function testI18nLoadShop()
    {
        $object = oxNew('oxI18n');
        $object->init('oxshops');

        $object->load($this->shopId);
        $this->assertSame('Ihre Bestellung bei OXID eSales', $object->oxshops__oxordersubject->value);
    }

    /**
     * Test loading from main view table.
     */
    public function testI18nLoadShopInLang()
    {
        $object = oxNew('oxI18n');
        $object->init('oxshops');

        $object->loadInLang('en', $this->shopId);
        $this->assertSame('Your order at OXID eShop', $object->oxshops__oxordersubject->value);
    }

    /**
     * Test if oxshop object works.
     */
    public function testShopSaveMultilingualObject()
    {
        $shop = $this->getProxyClass('oxShop');
        $shop->load($this->shopId);
        $this->assertSame('Ihre Bestellung bei OXID eSales', $shop->oxshops__oxordersubject->value);

        $shop->oxshops__oxordersubject = new oxField('test text');
        $shop->save('test text');
        $shop->load($this->shopId);
        $this->assertSame('test text', $shop->oxshops__oxordersubject->value);

        $query = "SELECT oxlang, oxordersubject FROM oxshops_multilang WHERE oxobjectid = '" . $this->shopId . "'";
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getArray($query);

        $expected = array( array('oxlang'         => 'de',
                                 'oxordersubject' => 'test text'),
                           array('oxlang'         => 'en',
                                 'oxordersubject' => 'Your order at OXID eShop'));

        $this->assertSame($expected, $result);
    }

    /**
     * Test if oxshop object works.
     */
    public function testShopLoadMultilingualObjectInDifferentLanguage()
    {
        $shop = $this->getProxyClass('oxShop');

        $shop->loadInLang('en', $this->shopId);
        $this->assertSame('Your order at OXID eShop', $shop->oxshops__oxordersubject->value);
    }

    /**
     * Test changing category multilanguage fields.
     */
    public function testCategoryUpdateTitle()
    {
        $categoryId = $this->getCategoryId();
        $category = oxNew('oxCategory');

        $category->load($categoryId);
        $this->assertSame('Wohnen', $category->oxcategories__oxtitle->value);
        $category->oxcategories__oxtitle = new oxField('Daheim');
        $category->save();

        $category->loadInLang('de', $categoryId);
        $this->assertSame('Daheim', $category->oxcategories__oxtitle->value);
        $category->oxcategories__oxtitle = new oxField('daheim wohnen');
        $category->save();

        $category->loadInLang('en', $categoryId);
        $this->assertSame('Living', $category->oxcategories__oxtitle->value);
        $category->oxcategories__oxtitle = new oxField('home sweet home');
        $category->save();

        $query = "SELECT oxlang, oxtitle FROM oxcategories_multilang WHERE oxobjectid = '{$categoryId}'";
        if ('EE' == $this->getTestConfig()->getShopEdition()) {
            $query = "SELECT oxlang, oxtitle FROM oxcategories_multilang WHERE oxmapobjectid = '{$category->oxcategories__oxmapid->value}'";
        }
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getArray($query);

        $expected = array( array('oxlang'  => 'de',
                                 'oxtitle' => 'daheim wohnen'),
                           array('oxlang'  => 'en',
                                 'oxtitle' => 'home sweet home'));

        $this->assertSame($expected, $result);
    }

    /**
     * Test inserting category multilanguage fields.
     */
    public function testCategoryInsertTitleForNewLanguage()
    {
        $languageId = 'zz';
        $this->configureNewLanguage($languageId);
        $metaDataHandler = oxNew('oxDbMetaDataHandler');
        $metaDataHandler->updateViews();

        $categoryId = $this->getCategoryId();
        $category = oxNew('oxCategory');

        $category->loadInLang($languageId, $categoryId);
        $this->assertNull($category->oxcategories__oxtitle->value);
        $category->oxcategories__oxtitle = new oxField('new language text');
        $category->save();

        $query = "SELECT oxlang, oxtitle FROM oxcategories_multilang WHERE oxobjectid = '{$categoryId}' and oxlang = 'zz'";
        $result = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getArray($query);

        $expected = array( array('oxlang'  => $languageId,
                                 'oxtitle' => 'new language text'));

        $this->assertSame($expected, $result);
    }

    /**
     * Test changing article longdesciption
     */
    public function testArticleUpdateLongdescription()
    {
        $text = 'some long description';
        $article = oxNew('oxarticle');
        $article->load('1126');
        $article->setArticleLongDesc($text);
        $article->save();

        $article = oxNew('oxarticle');
        $article->load('1126');
        $this->assertSame($text, $article->getLongDesc());
    }

    /**
     * Test setting article tags.
     */
    public function testArticleTags()
    {
        $articleTagList = oxNew('oxarticletaglist');
        $articleTagList->loadInLang('en', '531f91d4ab8bfb24c4d04e473d246d0b');
        $tags = array_keys($articleTagList->getArray());

        $expected = array('jeans', 'kuyichi', 'stylish', 'dark');
        $this->assertSame($expected, $tags);

        $articleTagList->set('absinth,newtag');
        $articleTagList->save();

        //load after change
        $articleTagList = oxNew('oxarticletaglist');
        $articleTagList->loadInLang('en', '531f91d4ab8bfb24c4d04e473d246d0b');
        $tags = array_keys($articleTagList->getArray());

        $expected = array('absinth', 'newtag');
        $this->assertEquals($expected, $tags);
    }

    /**
     * Test creating an article including long description.
     */
    public function testCreateArticleDefaultLanguage()
    {
        $text = 'some german long description';
        $testArticleId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );

        $article = oxNew('oxarticle');
        $article->setId($testArticleId);
        $article->oxarticles__oxartnum = new oxField('666-T', oxField::T_RAW);
        $article->oxarticles__oxtitle  = new oxField('TEST_MULTI_LANGUAGE', oxField::T_RAW);
        $article->setArticleLongDesc($text);
        $article->save();

        $article = oxNew('oxarticle');
        $article->load($testArticleId);
        $this->assertSame($text, $article->getLongDesc());

    }

    /**
     * Test creating an article including long description.
     */
    public function testCreateArticleNonDefaultLanguage()
    {
        $text = 'some english long description';
        $testArticleId = substr_replace( oxUtilsObject::getInstance()->generateUId(), '_', 0, 1 );

        $article = oxNew('oxarticle');
        $article->setId($testArticleId);
        $article->oxarticles__oxartnum = new oxField('666-T', oxField::T_RAW);
        $article->oxarticles__oxtitle  = new oxField('TEST_MULTI_LANGUAGE', oxField::T_RAW);
        $article->save();

        $article->loadInLang('en', $testArticleId);
        $article->setArticleLongDesc($text);
        $article->save();

        $article = oxNew('oxarticle');
        $article->loadInLang('en', $testArticleId);
        $this->assertSame($text, $article->getLongDesc());
    }

    /**
     * Test loading contents.
     */
    public function testContents()
    {
        $contents = oxNew('oxContent');

        $contents->loadByIdent('oxforgotpwd');
        $this->assertTrue(0 < strlen($contents->oxcontents__oxcontent->value));

        $this->markTestIncomplete('this special contents case is not yet working');
        $contents->loadByIdent('oxcredits');
        $this->assertTrue(0 < strlen($contents->oxcontents__oxcontent->value));

    }

    /**
     * Test loading category attributes.
     */
    public function testCategoryAttributes()
    {
        $categoryId = '8a142c3e49b5a80c1.23676990';
        $attributeList = oxNew('oxAttributeList');

        $attributeList->getCategoryAttributes($categoryId, 'de');
        $this->assertSame(array(), $attributeList->getArray());
    }

    /**
     * Test getting the correct table set for given language abbreviation.
     */
    public function testDbMetaDataHandlerGetMultilangFields()
    {
        $metaDataHandler = oxNew('oxDbMetaDataHandler');

        $expected = array(
            'OXVARNAME',
            'OXVARSELECT',
            'OXTITLE',
            'OXSHORTDESC',
            'OXURLDESC',
            'OXSEARCHKEYS',
            'OXSTOCKTEXT',
            'OXNOSTOCKTEXT'
        );

        $result = $metaDataHandler->getMultilangFields('oxarticles');
        $this->assertSame(sort($expected), sort($result));
    }

    /**
     * Test getting oxarticles table's multilanguage fields.
     */
    public function testDbMetaDataHandlerGetMultilangFieldsOxArticles()
    {
        $metaDataHandler = oxNew('oxDbMetaDataHandler');

        $expected = array('OXVARNAME',
                          'OXVARSELECT',
                          'OXTITLE',
                          'OXSHORTDESC',
                          'OXURLDESC',
                          'OXSEARCHKEYS',
                          'OXSTOCKTEXT',
                          'OXNOSTOCKTEXT');

        $result =  $metaDataHandler->getMultilangFields('oxarticles');
        $this->assertSame(sort($expected), sort($result));
    }

    /**
     * Test getting additional test table's multilanguage fields.
     */
    public function testDbMetaDataHandlerGetMultilangFieldsAdditionalTable()
    {
        $this->createTestTables('addtest', 'title');
        $metaDataHandler = oxNew('oxDbMetaDataHandler');

        $expected = array('TITLE',
                          'SHORTDESC');
        $this->assertSame($expected, $metaDataHandler->getMultilangFields('addtest'));
    }

    /*
     * Test generating view names.
     */
    public function testViewNameGeneratorViewNames()
    {
        $language             = oxNew('oxLang');
        $languageAbbreviation = $language->getLanguageAbbr('de');
        $this->assertEquals('de', $languageAbbreviation);

        $viewNameGenerator = oxRegistry::get('oxTableViewNameGenerator');
        $viewName = $viewNameGenerator->getViewName('oxshops', 'de', $this->shopId);
        $this->assertEquals('oxv_oxshops_de', $viewName);
    }

    /**
     * Test getting single language fields
     */
    public function testDbMetaDataHandlerGetSingleLanguageFields()
    {
        $metaDataHandler = oxNew('oxDbMetaDataHandler');

        //core table
        $fields = $metaDataHandler->getSinglelangFields('oxshops', 'de');
        $this->assertFalse(isset($fields['OXTITLEPREFIX_FR']));
        $this->assertSame($fields['OXTITLEPREFIX'], 'oxshops_multilang.OXTITLEPREFIX');

        //generic view
        $fields = $metaDataHandler->getSinglelangFields('oxv_oxshops', 'de');
        $this->assertSame($fields['OXTITLEPREFIX'], 'oxv_oxshops.OXTITLEPREFIX_DE');

        //language specific view
        $fields = $metaDataHandler->getSinglelangFields('oxv_oxshops_DE', 'de');
        $this->assertSame($fields['OXTITLEPREFIX'], 'oxv_oxshops_de.OXTITLEPREFIX');

        //core table
        $fields = $metaDataHandler->getSinglelangFields('oxshops', 'en');
        $this->assertSame($fields['OXTITLEPREFIX'], 'oxshops_multilang.OXTITLEPREFIX');

        //generic view
        $fields = $metaDataHandler->getSinglelangFields('oxv_oxshops', 'en');
        $this->assertSame($fields['OXTITLEPREFIX'], 'oxv_oxshops.OXTITLEPREFIX_EN');

        //language specific view
        $fields = $metaDataHandler->getSinglelangFields('oxv_oxshops_EN', 'en');
        $this->assertSame($fields['OXTITLEPREFIX'], 'oxv_oxshops_en.OXTITLEPREFIX');

    }

    /**
     * Test view generating query parts.
     */
    public function testShopGetViewJoinLang()
    {
        $shop = $this->getProxyClass('oxShop');
        $shop->load($this->shopId);
        $result = $shop->_getViewJoinLang('oxshops', 'de');
        $expected = " LEFT JOIN oxshops_multilang ON (oxshops.OXID = oxshops_multilang.OXOBJECTID AND oxshops_multilang.OXLANG = 'de') ";
        $this->assertSame($expected, $result);
    }

    /**
     * Test view generating query parts.
     */
    public function testShopGetViewJoinAll()
    {
        $this->createTestTables('addtest');

        $shop = $this->getProxyClass('oxShop');
        $shop->load($this->shopId);
        $result = $shop->_getViewJoinAll('addtest');

        $expected = " LEFT JOIN addtest_multilang AS mlang_de ON (addtest.OXID = mlang_de.OXOBJECTID AND mlang_de.OXLANG = 'de') " .
                    "LEFT JOIN addtest_multilang AS mlang_en ON (addtest.OXID = mlang_en.OXOBJECTID AND mlang_en.OXLANG = 'en') ";

        $this->assertSame($expected, $result);
    }

    /**
     * Test view generating query parts.
     */
    public function testShopGetViewSelectForSingleLanguage()
    {
        $this->createTestTables('addtest');

        $shop = $this->getProxyClass('oxShop');
        $shop->load($this->shopId);
        $result = $shop->_getSingleLanguageViewSelect('addtest');

        $expected = "addtest.OXID,addtest.OXTIMESTAMP,addtest_multilang.TITLE,addtest_multilang.SHORTDESC";
        $this->assertSame($expected, $result);
    }

    /**
     * Test view generating query parts.
     */
    public function testShopGetViewSelectAll()
    {
        $this->createTestTables('addtest');

        $shop = $this->getProxyClass('oxShop');
        $shop->load($this->shopId);
        $result = $shop->_getViewSelectAll('addtest');

        $expected = "addtest.OXID,addtest.OXTIMESTAMP," .
                    "mlang_de.TITLE AS TITLE_DE,mlang_de.SHORTDESC AS SHORTDESC_DE," .
                    "mlang_en.TITLE AS TITLE_EN,mlang_en.SHORTDESC AS SHORTDESC_EN";

        $this->assertSame($expected, $result);
    }

    /**
     * Test generating views.
     */
    public function testShopGenerateViewsCreatesCorrectViews()
    {
        $oShop = oxNew('oxShop');
        $oShop->load($this->shopId);

        $oShop->generateViews();

        $dbMetaDataHandler = oxNew("oxDbMetaDataHandler");
        $this->assertTrue($dbMetaDataHandler->tableExists('oxv_oxarticles_de'));
        $this->assertTrue($dbMetaDataHandler->fieldExists('oxtitle', 'oxv_oxarticles_de'));
        $this->assertTrue($dbMetaDataHandler->fieldExists('oxtitle_de', 'oxv_oxarticles'));
    }

    /**
     * Test generating views.
     */
    public function testDbMetaDataHandlerUpdateViewsCreatesCorrectViews()
    {
        $newLanguage = 'aa';
        $this->configureNewLanguage($newLanguage);

        $dbMetaDataHandler = oxNew("oxDbMetaDataHandler");
        $dbMetaDataHandler->updateViews();

        $this->assertTrue($dbMetaDataHandler->tableExists('oxv_oxarticles_aa'));
        $this->assertTrue($dbMetaDataHandler->fieldExists('oxtitle', 'oxv_oxarticles_aa'));
        $this->assertTrue($dbMetaDataHandler->fieldExists('oxtitle_aa', 'oxv_oxarticles'));

    }

    /**
     * Test adding a brand new language like it would be done via shop admin.
     */
    public function testLanguageMainAddNewLanguage()
    {
        $languageMain = oxNew('language_main');

        $parameters = array(
            'oxid'       => '-1',
            'active'     => '1',
            'abbr'       => 'aa',
            'desc'       => 'AAA',
            'baseurl'    => '',
            'basesslurl' => '',
            'sort'       => ''
        );

        $this->setRequestParameter('oxid', '-1');
        $this->setRequestParameter('editval', $parameters);
        $languageMain->save();

        $dbMetaDataHandler = oxNew("oxDbMetaDataHandler");
        $dbMetaDataHandler->updateViews();

        $this->assertTrue($dbMetaDataHandler->tableExists('oxv_oxarticles_aa'));
        $this->assertTrue($dbMetaDataHandler->fieldExists('oxtitle_aa', 'oxv_oxarticles'));
        $this->assertTrue($dbMetaDataHandler->fieldExists('oxtitle', 'oxv_oxarticles_aa'));
    }

    /**
     * Test adding language abbreviation again but ucfirst.
     */
    public function testLanguageMainAddSameLanguageAbbreviationAgainCaseInsensitive()
    {
        $expectedException = oxNew('oxExceptionToDisplay');
        $expectedException->setMessage('LANGUAGE_ALREADYEXISTS_ERROR');

        $utilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $utilsView->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo($expectedException));
        oxRegistry::set('oxUtilsView', $utilsView);

        $languageMain = oxNew('language_main');

        $parameters = array(
            'oxid'       => 'De',
            'active'     => '1',
            'abbr'       => 'De',
            'desc'       => 'German',
            'baseurl'    => '',
            'basesslurl' => '',
            'sort'       => ''
        );

        $this->setRequestParameter('oxid', '-1');
        $this->setRequestParameter('editval', $parameters);
        $languageMain->save();
    }

    /**
     * Test changing a language abbreviation.
     */
    public function testLanguageMainForbiddenToChangeLanguageAbbreviation()
    {
        $expectedException = oxNew('oxExceptionToDisplay');
        $expectedException->setMessage('LANGUAGE_ABBRCHANGELANG_WARNING');

        $utilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $utilsView->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo($expectedException));
        oxRegistry::set('oxUtilsView', $utilsView);

        $languageMain = oxNew('language_main');

        $parameters = array(
            'oxid'       => 'de',
            'active'     => '1',
            'abbr'       => 'zz',
            'desc'       => 'German',
            'baseurl'    => '',
            'basesslurl' => '',
            'sort'       => ''
        );

        $this->setRequestParameter('oxid', 'de');
        $this->setRequestParameter('editval', $parameters);
        $languageMain->save();
    }

    /**
     * Test deleting a language.
     */
    public function testLanguageMainDeleteLanguage()
    {
        $this->setRequestParameter('oxid', 'en');

        $languageList = oxNew('Language_List');
        $languageList->deleteEntry();

        $languages = $this->getProxyClass('LanguageMainHelper')->_getLanguages();
        $this->assertFalse(isset($languages['params']['en']));

        $dbMetaDataHandler = oxNew("oxDbMetaDataHandler");
        $dbMetaDataHandler->updateViews();

        $this->assertTrue($dbMetaDataHandler->fieldExists('oxtitle_de', 'oxv_oxarticles'));
        $this->assertFalse($dbMetaDataHandler->fieldExists('oxtitle_en', 'oxv_oxarticles'));
        $this->assertFalse($dbMetaDataHandler->tableExists('oxv_oxarticles_en'));
    }

    /**
     * Test deleting a language.
     */
    public function testLanguageMainDeleteMainLanguageForbidden()
    {
        $expectedException = oxNew('oxExceptionToDisplay');
        $expectedException->setMessage('LANGUAGE_DELETINGMAINLANG_WARNING');

        $utilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $utilsView->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo($expectedException));
        oxRegistry::set('oxUtilsView', $utilsView);

        $this->setRequestParameter('oxid', 'de');

        $languageList = oxNew('Language_List');
        $languageList->deleteEntry();

        $languages = $this->getProxyClass('LanguageMainHelper')->_getLanguages();
        $this->assertTrue(isset($languages['params']['en']));

    }

    /**
     * Test getting multilanguage extension tables.
     */
    public function testDbMetaDataHandlerGetAllMultiTables()
    {
        $this->createTestTables('addtest');
        $this->setConfigParam('aMultiLangTables', array('addtest'));

        $metaDataHandler = oxNew('oxDbMetaDataHandler');

        $expected = array('addtest_multilang');
        $this->assertSame($expected, $metaDataHandler->getAllMultiTables('addtest'));
    }

    /**
     * Restructure database.
     */
    protected function restructureDatabase()
    {
        $testConfig = new \OxidEsales\TestingLibrary\TestConfig();
        $serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();

        if (('CE' == $this->getTestConfig()->getShopEdition()) || ('PE' == $this->getTestConfig()->getShopEdition())) {
            $serviceCaller->setParameter('importSql', '@'. $testConfig->getShopTestsPath() .'/Fixtures/restructured_database.sql');
        }

        if ('EE' == $this->getTestConfig()->getShopEdition()) {
            $serviceCaller->setParameter('importSql', '@'. getShopBasePath() .'/Edition/Enterprise/tests/Fixtures/restructured_database.sql');
        }

        $serviceCaller->callService('ShopPreparation', 1);
    }

    /**
     * Restore database to whatever state it was in at beginning of this test.
     */
    protected function prepareDatabase()
    {
        $dbMetaDataHandler = oxNew('oxDbMetaDataHandler');
        $this->originalTables = $dbMetaDataHandler->getAllTables();

        foreach ($this->originalTables as $table) {
            $this->originalFields[$table] = array_keys($dbMetaDataHandler->getFields($table));
        }
    }

    /**
     * Test helper to create additional multilanguage table.
     *
     * @param string $tableName
     */
    protected function createTestTables($tableName = 'addtest')
    {
        $queries = array();

        $queries[0] = "CREATE TABLE IF NOT EXISTS `" . $tableName . "` (" .
                      "`OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Item id'," .
                      "`OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp'," .
                      "PRIMARY KEY (`OXID`)" .
                      ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='for testing'";

        $queries[1] = "CREATE TABLE IF NOT EXISTS `" . $tableName . "_multilang` (" .
                      "`OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Item id'," .
                      "`OXLANG` VARCHAR(32) NOT NULL default 'de' COMMENT 'Language id'," .
                      "`TITLE` char(255) NOT NULL default '' COMMENT 'Title (multilanguage)'," .
                      "`SHORTDESC` char(255) NOT NULL default '' COMMENT 'Short description (multilanguage)'," .
                      "`OXTIMESTAMP` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP COMMENT 'Timestamp'," .
                      "UNIQUE KEY `OXIDLANG` (`OXID`,`OXLANG`)" .
                      ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='for testing'";

        foreach ($queries as $query) {
            oxDb::getDb()->query($query);
            oxDb::getInstance()->getTableDescription($tableName); //throws exception if table does not exist
            $this->additionalTables[$tableName] = $tableName;
        }

    }

    /**
     * Test helper to insert a new language with given id into language configuration.
     *
     * @param $languageId
     *
     * @return integer
     */
    protected function configureNewLanguage($languageId)
    {
        $languageMain = $this->getProxyClass('LanguageMainHelper');
        $languages = $languageMain->_getLanguages();
        $sort = (count($languages['lang']) + 1) * 100;

        $languages['params'][$languageId] = array('baseId' => $languageId,
                                                  'active' => 1,
                                                  'sort'   => $sort);

        $languages['lang'][$languageId] = $languageId;
        $languages['urls'][$languageId]     = '';
        $languages['sslUrls'][$languageId]  = '';
        $languageMain->setLanguageData($languages);

        $this->storeLanguageConfiguration($languages);

        oxRegistry::set('oxLang', null);
        oxRegistry::set('oxTableViewNameGenerator', null);
    }

    /**
     * Test helper for saving language configuration.
     *
     * @param $languages
     */
    protected function storeLanguageConfiguration($languages, $defaultLanguage = 'de')
    {
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $languages['params']);
        $this->getConfig()->saveShopConfVar('aarr', 'aLanguages', $languages['lang']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageURLs', $languages['urls']);
        $this->getConfig()->saveShopConfVar('arr', 'aLanguageSSLURLs', $languages['sslUrls']);
        $this->getConfig()->saveShopConfVar('str', 'sDefaultLang', $defaultLanguage);
    }

    /**
     * Get view table according to shop edition.
     *
     * @return string
     */
    private function getViewTable($table, $language)
    {
        return 'EE' == $this->getTestConfig()->getShopEdition() ? "oxv_{$table}_1_{$language}" : "oxv_{$table}_{$language}";
    }

    /**
     * Get category id depending on shop edition.
     */
    private function getCategoryId()
    {
        return 'EE' == $this->getTestConfig()->getShopEdition() ? '30e44ab83b6e585c9.63147165' : '8a142c3e44ea4e714.31136811';
    }
}

