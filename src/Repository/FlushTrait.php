<?php

namespace App\Repository;

trait FlushTrait
{
    public function flush(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
}
