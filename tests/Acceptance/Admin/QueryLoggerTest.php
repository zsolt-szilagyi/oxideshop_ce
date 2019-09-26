<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;
use OxidEsales\TestingLibrary\helpers\ExceptionLogFileHelper;
use Webmozart\PathUtil\Path;

/**
 * Class QueryLoggerTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Acceptance\Admin
 */
class QueryLoggerTest extends AdminTestCase
{

    /**
     * @var ExceptionLogFileHelper
     */
    private $adminLogHelper;

    protected function setUp()
    {
        parent::setUp();

        $this->enableAdminLog();
        $this->adminLogHelper = new ExceptionLogFileHelper(Path::join(OX_BASE_PATH, 'log', 'oxadmin.log'));
        $this->adminLogHelper->clearExceptionLogFile();
    }

    protected function tearDown()
    {
        $this->adminLogHelper->clearExceptionLogFile();
        parent::tearDown();
    }

    /**
     * Verify that shop frontend is ok with enabled admin log.
     */
    public function testShopFrontendWithAdminLogEnabled()
    {
        $this->openShop();
        $this->checkForErrors();

        $this->assertEmpty($this->adminLogHelper->getExceptionLogFileContent());
    }

    /**
     * Verify that shop admin is ok with enabled admin log.
     */
    public function testShopAdminWithAdminLogEnabled()
    {
        $this->loginAdmin('Master Settings', 'Core Settings');
        $this->openTab("System");
        $this->click("link=Other settings");
        $this->type('confarrs[aLogSkipTags]', 'asdf');
        $this->clickAndWait("save");
        $this->type('confarrs[aLogSkipTags]', '');
        $this->clickAndWait("save");

        $logged = $this->adminLogHelper->getExceptionLogFileContent();

        $this->assertNotEmpty($logged);

        $this->assertContains('query:', strtolower($logged));

        $this->assertTrue(false !== stripos($logged, 'function:'));
        $this->assertTrue(false !== stripos($logged, 'aLogSkipTags'));
    }

    /**
     * Test helper
     */
    private function enableAdminLog()
    {
        $this->callShopSC(
            "oxConfig",
            null,
            null,
            [
                'blLogChangesInAdmin' => true
            ]
        );
    }
}
