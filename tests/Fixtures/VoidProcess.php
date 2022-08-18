<?php

namespace App\Tests\Fixtures;

use Symfony\Component\Process\Process;

class VoidProcess extends Process
{
    public function start(callable $callback = null, array $env = []): void
    {
    }

    public function wait(callable $callback = null): int
    {
        return 0;
    }

    public function isSuccessful(): bool
    {
        return true;
    }
}
