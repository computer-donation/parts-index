<?php

namespace App\Graph\Node;

use App\Graph\GraphHelper;

class ProbeRepository
{
    public function __construct(protected GraphHelper $graphHelper)
    {
    }

    public function setUp(): void
    {
        $this->graphHelper->rawQuery('CREATE INDEX ON :Probe(id)');
        // $this->graphHelper->rawQuery('CREATE CONSTRAINT ON (c:Probe) ASSERT c.id IS UNIQUE');
    }
}
