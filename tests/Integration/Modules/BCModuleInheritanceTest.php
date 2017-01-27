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
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\EshopCommunity\Core\Registry;

/**
 * THIS IS A DRAFT
 */
class BCModuleInheritanceTest extends BaseModuleTestCase
{

    /**
     * @var Environment The helper object for the environment.
     */
    protected $environment = null;

    /**
     * Standard set up method. Calls parent first.
     */
    public function setUp()
    {
        parent::setUp();

        $configFile = Registry::get('oxConfigFile');
        $configFile->setVar('sShopDir', realpath(__DIR__ . '/TestData'));

        Registry::set('oxConfigFile', $configFile);

        $this->environment = new Environment();
    }

    /**
     * Standard tear down method. Calls parent last.
     */
    public function tearDown()
    {
        $this->environment->clean();

        parent::tearDown();
    }

    /**
     * This test covers PHP inheritance between one module class and one shop class.
     *
     * The module class extends the PHP class directly like '<module class> extends <shop class>'
     * In this case the module class must be an instance of the shop class
     *
     * @dataProvider dataProviderTestModuleInheritanceTestPhpInheritance
     */
    public function testModuleInheritanceTestPhpInheritance($moduleToActivate, $moduleClassName, $shopClassName )
    {
        $this->environment->prepare([$moduleToActivate]);

        $modelClass = oxNew($moduleClassName);

        $this->assertInstanceOf($shopClassName, $modelClass);
    }

    public function dataProviderTestModuleInheritanceTestPhpInheritance() {
        return [
            [
                'moduleToActivate' => 'bc_module_inheritance_1_1',
                'moduleClassName' => 'vendor_1_module_1_myclass',
                'shopClassName' => 'oxArticle'
            ],
            /**
             * ETC.
            [
            'moduleToActivate' => 'bc_module_inheritance_1_2',
            'moduleClassName' => 'vendor_1_module_1_myclass',
            'shopClassName' => \OxidEsales\EshopCommunity\Application\Model\Article::class
            ],

             */
        ];
    }
}
