<?php

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Core\Modules\ModulesInstallerHelper;

/**
 * Class EditionPathProviderTest
 */
class ModulesInstallerHelperTest extends UnitTestCase
{
    public function testParseTemplateBlockValuesReturnsUniqueIdOnSecondExecution()
    {
        $firstResult = ModulesInstallerHelper::parseTemplateBlockValues();
        $secondResult = ModulesInstallerHelper::parseTemplateBlockValues();

        $this->assertNotSame($firstResult['id'], $secondResult['id']);
    }
}
