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

namespace OxidEsales\Eshop\Tests\Acceptance;

use OxidEsales\Eshop\Core\Edition\EditionSelector;
use OxidEsales\TestingLibrary\TestSqlPathProvider;

class AcceptanceTestCase extends \OxidEsales\TestingLibrary\AcceptanceTestCase
{
    /**
     * Adds tests sql data to database.
     *
     * @param string $sTestSuitePath
     */
    public function addTestData($sTestSuitePath)
    {
        parent::addTestData($sTestSuitePath);

        $editionSelector = new EditionSelector();

        if ($editionSelector->isEnterprise()) {
            $testSqlPathProvider = new TestSqlPathProvider(new EditionSelector(), $this->getTestConfig()->getShopPath());
            $sTestSuitePath = realpath($testSqlPathProvider->getDataPathBySuitePath($sTestSuitePath));

            $sFileName = $sTestSuitePath . '/demodata_' . SHOP_EDITION . '.sql';
            if (file_exists($sFileName)) {
                $this->importSql($sFileName);
            }

            if (isSUBSHOP && file_exists($sTestSuitePath . '/demodata_EE_mall.sql')) {
                $this->importSql($sTestSuitePath . '/demodata_EE_mall.sql');
            }
        }

    }

    /**
     * Sets up shop before running test case.
     * Does not use setUpBeforeClass to keep this method non-static.
     *
     * @param string $testSuitePath
     */
    public function setUpTestsSuite($testSuitePath)
    {
        if (!self::$testsSuiteStarted) {
            self::$testsSuiteStarted = true;
            $this->dumpDb('reset_suite_db_dump');
        } else {
            $this->restoreDb('reset_suite_db_dump');
        }

        $this->activateModules();
        $this->addTestData($testSuitePath);
        $this->restructureDatabase();

        $this->dumpDb('reset_test_db_dump');
    }

    /**
     * This method is called after the last test of this test class is run.
     *
     * @since Method available since Release 3.4.0
     */
    public static function tearDownAfterClass()
    {
        self::removeExtensionTables();
        self::restoreDefaultDatabase();

        parent::tearDownAfterClass();
    }

    /**
     * Restructure database after all test data is added.
     * TODO: change to adapted test data
     */
    protected function restructureDatabase()
    {
        $testConfig = new \OxidEsales\TestingLibrary\TestConfig();
        $serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
        $serviceCaller->setParameter('importSql', '@'. $testConfig->getShopTestsPath() .'/Fixtures/restructured_database.sql');

        if ($testConfig->getShopEdition() === 'EE') {
            $serviceCaller->setParameter('importSql', '@' . $testConfig->getShopPath() . '/Edition/Enterprise/Tests/Fixtures/restructured_database.sql');
        }

        $serviceCaller->callService('ShopPreparation', 1);
    }

    /**
     * Remove *_multilang tables.
     */
    protected static function removeExtensionTables()
    {
        $testConfig = new \OxidEsales\TestingLibrary\TestConfig();
        $serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
        $serviceCaller->setParameter('importSql', '@'. $testConfig->getShopTestsPath() .'/Fixtures/drop_extension_tables.sql');
        $serviceCaller->callService('ShopPreparation', 1);
    }

    /**
     * Reinstall old database.
     * We need this workaround atm until testdata is adapted to new structure.
     */
    protected static function restoreDefaultDatabase()
    {
        $testConfig = new \OxidEsales\TestingLibrary\TestConfig();
        $serviceCaller = new \OxidEsales\TestingLibrary\ServiceCaller();
        $serviceCaller->setParameter('importSql', '@'. $testConfig->getShopPath() .'/Setup/Sql/database.sql');

        if ($testConfig->getShopEdition() === 'PE') {
            $serviceCaller->setParameter('importSql', '@' . $testConfig->getShopPath() . '/Edition/Professional/Setup/Sql/database.sql');
        }
        if ($testConfig->getShopEdition() === 'EE') {
            $serviceCaller->setParameter('importSql', '@' . $testConfig->getShopPath() . '/Edition/Enterprise/Setup/Sql/database.sql');
        }

        $serviceCaller->callService('ShopPreparation', 1);
    }
}
