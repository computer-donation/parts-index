<?php

namespace App\Command;

use App\Entity\Probe;
use App\Repository\ProbeRepository;

trait ProbeTrait
{
    protected ProbeRepository $probeRepository;

    protected function getProbe(string $id): Probe
    {
        if (!$probe = $this->probeRepository->find($id)) {
            $probe = new Probe();
            $probe->id = $id;
            $this->probeRepository->add($probe, true);
        }

        return $probe;
    }
}
