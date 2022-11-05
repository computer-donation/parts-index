<?php

namespace App\Graph\Node;

use App\Graph\GraphHelper;
use App\Graph\QueryHelper;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Query;

class EthernetPciCardRepository
{
    public function __construct(protected QueryHelper $queryHelper, protected GraphHelper $graphHelper)
    {
    }

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :EthernetPciCard(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (e:EthernetPciCard) ASSERT e.id IS UNIQUE');
        $this->graphHelper->rawQuery("CALL db.idx.fulltext.createNodeIndex('EthernetPciCard', 'vendor', 'subVendor', 'device')");
    }

    public function create(string $ethernetId, string $vendor, ?string $subVendor, string $device, string $probeId): void
    {
        $ethernetVar = Query::variable(uniqid('ethernet_'));
        $probeVar = $this->queryHelper->track('Probe', $probeId);

        $set = new SetClause();
        $set->addAssignment($ethernetVar->assign(Query::map([
            'vendor' => Query::literal($vendor),
            'subVendor' => Query::literal($subVendor),
            'device' => Query::literal($device),
        ]))->setMutate());

        $ethernet = Query::node('EthernetPciCard')
            ->named($ethernetVar)
            ->withProperty('id', Query::literal($ethernetId));

        $this->queryHelper
            ->query()
            ->merge($ethernet, $set)
            ->merge($probeVar->relationshipTo(Query::node()->named($ethernetVar), 'HAS_ETHERNET_PCI'));
    }
}
