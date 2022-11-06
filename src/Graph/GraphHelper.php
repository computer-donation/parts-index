<?php

namespace App\Graph;

use Redislabs\Module\RedisGraph\Command\Delete;
use Redislabs\Module\RedisGraph\Query;
use Redislabs\Module\RedisGraph\RedisGraph;
use Redislabs\Module\RedisGraph\Result;

class GraphHelper
{
    protected RedisGraph $graph;

    public function __construct(
        string $redisGraphHost,
        int $redisGraphPort,
        protected string $redisGraphName,
        protected QueryHelper $queryHelper
    ) {
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

    public function query(?string $queryString = null): Result
    {
        $query = new Query($this->redisGraphName, $queryString ?? $this->queryHelper->build());

        return $this->graph->query($query);
    }

    public function rawQuery(?string $queryString = null): void
    {
        $query = new Query($this->redisGraphName, $queryString ?? $this->queryHelper->build());

        $this->graph->rawQuery($query);
    }
}
