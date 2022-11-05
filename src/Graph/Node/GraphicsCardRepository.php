<?php

namespace App\Graph\Node;

use App\Graph\GraphHelper;
use App\Graph\QueryHelper;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

class GraphicsCardRepository
{
    public function __construct(protected QueryHelper $queryHelper, protected GraphHelper $graphHelper)
    {
    }

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :GraphicsCard(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (g:GraphicsCard) ASSERT g.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('GraphicsCard', 'subVendor', 'device')");
    }

    public function create(string $gpuId, string $vendor, ?string $subVendor, string $device, string $probeId): void
    {
        $gpuVar = Query::variable(uniqid('gpu_'));
        $probeVar = $this->queryHelper->track('Probe', $probeId);

        $set = new SetClause();
        $set->addAssignment($gpuVar->assign(Query::map([
            'vendor' => Query::literal($vendor),
            'subVendor' => Query::literal($subVendor),
            'device' => Query::literal($device),
        ]))->setMutate());

        $gpu = Query::node('GraphicsCard')
            ->named($gpuVar)
            ->withProperty('id', Query::literal($gpuId));

        $this->queryHelper
            ->query()
            ->merge($gpu, $set)
            ->merge($probeVar->relationshipTo(Query::node()->named($gpuVar), 'HAS_GPU'));
    }
}
