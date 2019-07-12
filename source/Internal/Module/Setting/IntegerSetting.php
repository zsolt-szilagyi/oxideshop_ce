<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setting;

/**
 * @internal
 */
class IntegerSetting implements SettingInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $groupName = '';

    /**
     * @var int
     */
    private $positionInGroup = 1;

    /**
     * @var int
     */
    private $value;

    /**
     * @var array
     */
    private $constraints = [];

    public function __construct(string $name, int $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getPositionInGroup(): int
    {
        return $this->positionInGroup;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    public function setPositionInGroup(int $positionInGroup): void
    {
        $this->positionInGroup = $positionInGroup;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function setConstraints(array $constraints): void
    {
        $this->constraints = $constraints;
    }
}
