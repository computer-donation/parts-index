<?php

namespace App\Graph;

use Redislabs\Module\RedisGraph\Command\Delete;
use Redislabs\Module\RedisGraph\Query;
use Redislabs\Module\RedisGraph\RedisGraph;
use Redislabs\Module\RedisGraph\Result;

class GraphHelper
{
    protected RedisGraph $graph;

    public function __construct(string $redisGraphHost, int $redisGraphPort, protected string $redisGraphName)
    {
        $redis = new \Redis();
        $redis->connect($redisGraphHost, $redisGraphPort);

        $this->graph = RedisGraph::createWithPhpRedis($redis);
    }

    public function delete(): void
    {
        $this->graph->runCommand(
            Delete::createCommandWithArguments($this->redisGraphName)
        );
    }

    public function query(string $queryString, array $params = []): Result
    {
        $query = new Query($this->redisGraphName, $this->compileQueryString($queryString, $params));

        return $this->graph->query($query);
    }

    public function rawQuery(string $queryString, array $params = []): void
    {
        $query = new Query($this->redisGraphName, $this->compileQueryString($queryString, $params));

        $this->graph->rawQuery($query);
    }

    protected function compileQueryString(string $queryString, array $params = []): string
    {
        if (empty($params)) {
            return $queryString;
        }

        return strtr(
            $queryString,
            array_combine(
                array_map(fn (string $key) => '$'.$key, array_keys($params)),
                array_map(fn (string $value) => $this->quoteString($value), $params)
            )
        );
    }

    protected function quoteString(string $value): string
    {
        // Encode tabs, newlines, carriage returns and form feeds
        $value = str_replace(["\t", "\n", "\r", "\f"], ['\\t', '\\n', '\\r', '\\f'], $value);

        // Use single quotes
        return sprintf("'%s'", str_replace("'", "\'", $value));
    }
}
