<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setting;

/**
 * @internal
 */
interface SettingInterface
{
    public function getName(): string;

    public function getConstraints(): array;

    public function getGroupName(): string;

    public function getPositionInGroup(): int;
}
