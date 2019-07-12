<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setting;

use function is_string;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Utility\ShopSettingEncoderInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Common\Database\TransactionServiceInterface;
use OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class SettingDao implements SettingDaoInterface
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ShopSettingEncoderInterface
     */
    private $shopSettingEncoder;

    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @var TransactionServiceInterface
     */
    private $transactionService;

    /**
     * @var SettingFactoryInterface
     */
    private $settingFactory;

    /**
     * SettingDao constructor.
     * @param QueryBuilderFactoryInterface $queryBuilderFactory
     * @param ContextInterface $context
     * @param ShopSettingEncoderInterface $shopSettingEncoder
     * @param ShopAdapterInterface $shopAdapter
     * @param TransactionServiceInterface $transactionService
     * @param SettingFactoryInterface $settingFactory
     */
    public function __construct(
        QueryBuilderFactoryInterface $queryBuilderFactory,
        ContextInterface $context,
        ShopSettingEncoderInterface $shopSettingEncoder,
        ShopAdapterInterface $shopAdapter,
        TransactionServiceInterface $transactionService,
        SettingFactoryInterface $settingFactory
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->context = $context;
        $this->shopSettingEncoder = $shopSettingEncoder;
        $this->shopAdapter = $shopAdapter;
        $this->transactionService = $transactionService;
        $this->settingFactory = $settingFactory;
    }

    public function save(SettingInterface $setting, string $moduleId, int $shopId): void
    {
        $this->transactionService->begin();

        try {
            /**
             * The same entity was splitted between two tables.
             * Till we can't refactor tables we have to save data in both.
             */
            $this->deleteFromOxConfigTable($setting, $moduleId, $shopId);
            $this->deleteFromOxConfigDisplayTable($setting, $moduleId);

            $this->saveDataToOxConfigTable($setting, $moduleId, $shopId);
            $this->saveDataToOxConfigDisplayTable($setting, $moduleId, $shopId);

            $this->transactionService->commit();
        } catch (\Throwable $throwable) {
            $this->transactionService->rollback();
            throw $throwable;
        }
    }

    /**
     * @param ShopModuleSetting $shopModuleSetting
     */
    public function delete(SettingInterface $setting, string $moduleId, int $shopId): void
    {
        $this->deleteFromOxConfigTable($setting, $moduleId, $shopId);
    }

    /**
     * @param string $name
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return ShopModuleSetting
     * @throws EntryDoesNotExistDaoException
     */
    public function get(string $name, string $moduleId, int $shopId): SettingInterface
    {
        /**
         * The same entity was splitted between two tables.
         * Till we can't refactor tables we have to get data from both.
         */
        $settingsData = array_merge(
            $this->getDataFromOxConfigTable($name, $moduleId, $shopId),
            $this->getDataFromOxConfigDisplayTable($name, $moduleId)
        );

        $setting = $this->settingFactory->create(
            $settingsData['type'],
            $name,
            $this->shopSettingEncoder->decode($settingsData['type'], $settingsData['value'])
        );

        if (isset($settingsData['oxvarconstraint'])
            && is_string($settingsData['oxvarconstraint'])
            && $settingsData['oxvarconstraint'] !== ''
        ) {
            $setting->setConstraints(
                explode('|', $settingsData['oxvarconstraint'])
            );
        }

        if (isset($settingsData['oxgrouping'])) {
            $setting->setGroupName($settingsData['oxgrouping']);
        }

        if (isset($settingsData['oxpos'])) {
            $setting->setPositionInGroup(
                (int) $settingsData['oxpos']
            );
        }

        return $setting;
    }

    private function saveDataToOxConfigTable(SettingInterface $setting, string $moduleId, int $shopId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxconfig')
            ->values([
                'oxid'          => ':id',
                'oxmodule'      => ':moduleId',
                'oxshopid'      => ':shopId',
                'oxvarname'     => ':name',
                'oxvartype'     => ':type',
                'oxvarvalue'    => 'encode(:value, :key)',
            ])
            ->setParameters([
                'id'        => $this->shopAdapter->generateUniqueId(),
                'moduleId'  => $this->getPrefixedModuleId($moduleId),
                'shopId'    => $shopId,
                'name'      => $setting->getName(),
                'type'      => $this->getSettingType($setting),
                'value'     => $this->shopSettingEncoder->encode(
                    $this->getSettingType($setting),
                    $setting->getValue()
                ),
                'key'       => $this->context->getConfigurationEncryptionKey(),
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param ShopModuleSetting $shopModuleSetting
     */
    private function saveDataToOxConfigDisplayTable(SettingInterface $setting, string $moduleId, int $shopId)
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxconfigdisplay')
            ->values([
                'oxid'              => ':id',
                'oxcfgmodule'       => ':moduleId',
                'oxcfgvarname'      => ':name',
                'oxgrouping'        => ':groupName',
                'oxpos'             => ':position',
                'oxvarconstraint'   => ':constraints',
            ])
            ->setParameters([
                'id'            => $this->shopAdapter->generateUniqueId(),
                'moduleId'      => $this->getPrefixedModuleId($moduleId),
                'name'          => $setting->getName(),
                'groupName'     => $setting->getGroupName(),
                'position'      => $setting->getPositionInGroup(),
                'constraints'   => implode('|', $setting->getConstraints()),
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param string $name
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return array
     * @throws EntryDoesNotExistDaoException
     */
    private function getDataFromOxConfigTable(string $name, string $moduleId, int $shopId): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('decode(oxvarvalue, :key) as value, oxvartype as type, oxvarname as name')
            ->from('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxmodule = :moduleId')
            ->andWhere('oxvarname = :name')
            ->setParameters([
                'shopId'    => $shopId,
                'moduleId'  => $this->getPrefixedModuleId($moduleId),
                'name'      => $name,
                'key'       => $this->context->getConfigurationEncryptionKey(),
            ]);

        $result = $queryBuilder->execute()->fetch();

        if (false === $result) {
            throw new EntryDoesNotExistDaoException();
        }

        return $result;
    }

    /**
     * @param string $name
     * @param string $moduleId
     * @return array
     */
    private function getDataFromOxConfigDisplayTable(string $name, string $moduleId): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('oxgrouping, oxpos, oxvarconstraint')
            ->from('oxconfigdisplay')
            ->where('oxcfgmodule = :moduleId')
            ->andWhere('oxcfgvarname = :name')
            ->setParameters([
                'moduleId'  => $this->getPrefixedModuleId($moduleId),
                'name'      => $name,
            ]);

        $result = $queryBuilder->execute()->fetch();

        return $result ?? [];
    }

    private function deleteFromOxConfigTable(SettingInterface $setting, string $moduleId, int $shopId): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxconfig')
            ->where('oxshopid = :shopId')
            ->andWhere('oxvarname = :name')
            ->andWhere('oxmodule = :moduleId')
            ->setParameters([
                'shopId'    => $shopId,
                'name'      => $setting->getName(),
                'moduleId'  => $this->getPrefixedModuleId($moduleId),
            ]);

        $queryBuilder->execute();
    }

    private function deleteFromOxConfigDisplayTable(SettingInterface $setting, string $moduleId): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->delete('oxconfigdisplay')
            ->where('oxcfgmodule = :moduleId')
            ->andWhere('oxcfgvarname = :name')
            ->setParameters([
                'moduleId'  => $this->getPrefixedModuleId($moduleId),
                'name'      => $setting->getName(),
            ]);

        $queryBuilder->execute();
    }

    /**
     * @param string $moduleId
     * @return string
     */
    private function getPrefixedModuleId(string $moduleId): string
    {
        return 'module:' . $moduleId;
    }

    private function getSettingType(SettingInterface $setting): string
    {
        if ($setting instanceof BooleanSetting) {
            return ShopSettingType::BOOLEAN;
        }

        if ($setting instanceof IntegerSetting) {
            return ShopSettingType::INTEGER;
        }

        if ($setting instanceof StringSetting) {
            return ShopSettingType::STRING;
        }

        if ($setting instanceof ArraySetting) {
            return ShopSettingType::ASSOCIATIVE_ARRAY;
        }

        if ($setting instanceof PasswordSetting) {
            return ShopSettingType::PASSWORD;
        }

        if ($setting instanceof SelectSetting) {
            return ShopSettingType::SELECT;
        }
    }
}
