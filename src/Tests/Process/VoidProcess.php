<?php

namespace App\Tests\Process;

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Process\Process;

#[When(env: 'test')]
class VoidProcess extends Process
{
    public function __construct()
    {
        parent::__construct([]);
    }

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
