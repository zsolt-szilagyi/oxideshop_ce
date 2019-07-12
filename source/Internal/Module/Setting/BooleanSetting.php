<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setting;

/**
 * @internal
 */
class BooleanSetting implements SettingInterface
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
     * @var bool
     */
    private $value;

    public function __construct(string $name, bool $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getPositionInGroup(): int
    {
        return $this->positionInGroup;
    }

    public function getValue(): bool
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
}
