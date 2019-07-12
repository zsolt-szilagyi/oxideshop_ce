<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\Setting;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\ArraySetting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\BooleanSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\IntegerSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\PasswordSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SelectSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setting\StringSetting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SettingDaoTest extends TestCase
{
    use ContainerTrait;

    /**
     * @dataProvider settingValueDataProvider
     */
    public function testSave(SettingInterface $setting): void
    {
        $settingDao = $this->getShopModuleSettingDao();

        $settingDao->save($setting, 'testModuleId', 1);

        $this->assertEquals(
            $setting,
            $settingDao->get($setting->getName(), 'testModuleId', 1)
        );
    }

    public function testSaveSeveralSettings(): void
    {
        $settingDao = $this->getShopModuleSettingDao();

        $shopModuleSetting1 = new BooleanSetting('first', true);
        $shopModuleSetting1->setPositionInGroup(7);
        $shopModuleSetting1->setGroupName('group');

        $settingDao->save($shopModuleSetting1, 'testModuleId', 1);

        $shopModuleSetting2 = new BooleanSetting('second', true);
        $shopModuleSetting2->setPositionInGroup(7);
        $shopModuleSetting2->setGroupName('group');

        $settingDao->save($shopModuleSetting2, 'testModuleId', 1);

        $this->assertEquals(
            $shopModuleSetting1,
            $settingDao->get('first', 'testModuleId', 1)
        );

        $this->assertEquals(
            $shopModuleSetting2,
            $settingDao->get('second', 'testModuleId', 1)
        );
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException
     */
    public function testGetNonExistentSetting()
    {
        $settingDao = $this->getShopModuleSettingDao();

        $settingDao->get('onExistentSetting', 'moduleId', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException
     */
    public function testDelete()
    {
        $settingDao = $this->getShopModuleSettingDao();

        $setting = new StringSetting('testDelete', '');
        $settingDao->save($setting, 'testModuleId', 1);

        $settingDao->delete($setting, 'testModuleId', 1);
        $settingDao->get('testDelete', 'testModuleId', 1);
    }

    public function testUpdate()
    {
        $settingDao = $this->getShopModuleSettingDao();

        $setting = new StringSetting('testUpdate', 'valueBeforeUpdate');

        $settingDao->save($setting, 'testModuleId', 1);

        $setting->setValue('valueAfterUpdate');

        $settingDao->save($setting, 'testModuleId', 1);

        $this->assertEquals(
            $setting,
            $settingDao->get('testUpdate', 'testModuleId', 1)
        );
    }

    public function testUpdateDoesNotCreateDuplicationsInDatabase()
    {
        $moduleId = 'testModuleId';
        $settingName = 'testSettingName';

        $this->assertSame(0, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(0, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));

        $setting = new StringSetting($settingName, 'valueBeforeUpdate');

        $settingDao = $this->getShopModuleSettingDao();
        $settingDao->save($setting, 'testModuleId', 1);

        $this->assertSame(1, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(1, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));

        $setting->setValue('valueAfterUpdate');
        $settingDao->save($setting, 'testModuleId', 1);

        $this->assertSame(1, $this->getOxConfigTableRowCount($settingName, 1, $moduleId));
        $this->assertSame(1, $this->getOxDisplayConfigTableRowCount($settingName, $moduleId));
    }

    /**
     * Checks if DAO is compatible with OxidEsales\Eshop\Core\Config
     *
     * @dataProvider settingValueDataProvider
     */
    public function testBackwardsCompatibility(SettingInterface $setting): void
    {
        $settingDao = $this->getShopModuleSettingDao();

        $settingDao->save($setting, 'testModuleId', 1);

        $this->assertEquals(
            $settingDao->get($setting->getName(), 'testModuleId', 1)->getValue(),
            Registry::getConfig()->getShopConfVar($setting->getName(), 1, 'module:testModuleId')
        );
    }

    public function settingValueDataProvider(): array
    {
        return [
            [
                new BooleanSetting('bool', true),
            ],
            [
                new StringSetting('string', 'value'),
            ],
            [
                new PasswordSetting('password', 'value'),
            ],
            [
                new SelectSetting('select', 'value'),
            ],
            [
                new IntegerSetting('int', 7),
            ],
            [
                new ArraySetting('array', [1, 2,]),
            ],
        ];
    }

    private function getShopModuleSettingDao()
    {
        return $this->get(SettingDaoInterface::class);
    }

    private function getOxConfigTableRowCount(string $settingName, int $shopId, string $moduleId): int
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->select('*')
            ->from('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->andWhere('oxmodule = :moduleId')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $settingName,
                'moduleId'  => 'module:' . $moduleId,
            ]);

        return $queryBuilder->execute()->rowCount();
    }

    private function getOxDisplayConfigTableRowCount(string $settingName, string $moduleId): int
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder
            ->select('*')
            ->from('oxconfigdisplay')
            ->andWhere('oxcfgvarname = :name')
            ->andWhere('oxcfgmodule = :moduleId')
            ->setParameters([
                'name'      => $settingName,
                'moduleId'  => 'module:' . $moduleId,
            ]);

        return $queryBuilder->execute()->rowCount();
    }
}
