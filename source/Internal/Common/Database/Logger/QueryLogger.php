<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Database\Logger;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

/**
 * @internal
 */
class QueryLogger implements SQLLogger
{

    /**
     * @var double
     */
    private $startTime;

    /**
     * @var array
     */
    private $query = [];

    /**
     * @var LoggerInterface
     */
    private $psrLogger;

    /**
     * @var QueryFilter
     */
    private $queryFilter;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * QueryLogger constructor.
     *
     * @param QueryFilter      $queryFilter
     * @param ContextInterface $context
     * @param LoggerInterface  $psrLogger
     */
    public function __construct(
        QueryFilter $queryFilter,
        ContextInterface $context,
        LoggerInterface $psrLogger
    ) {
        $this->queryFilter = $queryFilter;
        $this->psrLogger = $psrLogger;
        $this->context = $context;
    }

    /**
     * Logs an SQL statement somewhere.
     *
     * @param string              $query  The query to be executed.
     * @param mixed[]|null        $params The query parameters.
     * @param int[]|string[]|null $types  The query parameter types.
     *
     * @return void
     */
    public function startQuery($query, ?array $params = null, ?array $types = null): void
    {
        $this->startTime = microtime(true);

        if ($this->filterPass($query)) {
            $this->setQueryData($query, (array) $params);
        }
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery(): void
    {
        if ($this->filterPass($this->query)) {
            $this->query['executionMS'] = microtime(true) - $this->startTime;
            $this->psrLogger->debug($this->getLogMessage());
        }
    }

    /**
     * Get first entry from backtrace that is not connected to database.
     * This has to be the origin of the query.
     *
     * @return array
     */
    private function getBackTraceItem(): array
    {
        $queryTraceItem = [];

        foreach ((new \Exception)->getTrace() as $item) {
            if ((false === stripos($item['class'], get_class($this))) &&
                (false === stripos($item['class'], 'Doctrine'))
            ) {
                $queryTraceItem = $item;
                break;
            }
        }

        return $queryTraceItem;
    }

    /**
     * Collect query information.
     *
     * @param string $query  The query to be executed.
     * @param array  $params The query parameters.
     */
    private function setQueryData($query, array $params = null): void
    {
        $backTraceInfo = $this->getBackTraceItem();
        $this->query = [
            'userId'      => $this->context->getAdminUserId(),
            'shopId'      => $this->context->getCurrentShopId(),
            'class'       => $backTraceInfo['class'] ?? '',
            'function'    => isset($backTraceInfo['function']) ? $backTraceInfo['function'] : '',
            'file'        => isset($backTraceInfo['file']) ? $backTraceInfo['file'] : '',
            'line'        => isset($backTraceInfo['line']) ? $backTraceInfo['line'] : '',
            'query'       => $query,
            'params'      => serialize($params),
            'executionMS' => 0,
        ];
    }

    /**
     * @return bool
     */
    private function filterPass(string $query): bool
    {
        return $this->queryFilter->shouldLogQuery($query, $this->context->getSkipLogTags());
    }

    /**
     * Assemble log message
     *
     * @return string
     */
    private function getLogMessage(): string
    {
        $message = '';

        foreach ($this->query as $key => $value) {
            $message .= PHP_EOL . $key . ': ' . $value;
        }

        return $message . PHP_EOL;
    }
}
