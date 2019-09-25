<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Database\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

/**
 * @internal
 */
class NullLogger implements SQLLogger
{
    /**
     * Does not log anything.
     *
     * @param string              $query  The query to be executed.
     * @param mixed[]|null        $params The query parameters.
     * @param int[]|string[]|null $types  The query parameter types.
     *
     * @return void
     */
    public function startQuery($query, ?array $params = null, ?array $types = null): void
    {
        //do nothing
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery(): void
    {
        //do nothing
    }
}
