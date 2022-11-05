<?php

namespace App\Graph\Node;

use App\Graph\GraphHelper;
use App\Graph\QueryHelper;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

class CpuRepository
{
    public function __construct(protected QueryHelper $queryHelper, protected GraphHelper $graphHelper)
    {
    }

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Cpu(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (c:Cpu) ASSERT c.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('Cpu', 'vendor', 'model')");
    }

    public function create(string $cpuId, string $vendor, string $model, string $probeId): void
    {
        $cpuVar = Query::variable(uniqid('cpu_'));
        $probeVar = $this->queryHelper->track('Probe', $probeId);

        $set = new SetClause();
        $set->addAssignment($cpuVar->assign(Query::map([
            'vendor' => Query::literal($vendor),
            'model' => Query::literal($model),
        ]))->setMutate());

        $cpu = Query::node('Cpu')
            ->named($cpuVar)
            ->withProperty('id', Query::literal($cpuId));

        $this->queryHelper
            ->query()
            ->merge($cpu, $set)
            ->merge($probeVar->relationshipTo(Query::node()->named($cpuVar), 'HAS_CPU'));
    }
}
