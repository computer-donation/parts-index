<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

trait RepoTrait
{
    protected ?Process $process;

    abstract protected function getRepo(): string;

    abstract protected function getDir(): string;

    protected function updateRepository(OutputInterface $output): void
    {
        $output->writeln('Updating repository...');
        if (!is_dir($this->getDir())) {
            $this->cloneRepo();
        } else {
            $this->pullRepo();
        }
    }

    protected function cloneRepo(): void
    {
        $process = $this->process ?? new Process(['git', 'clone', '--progress', $this->getRepo(), $this->getDir()]);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
        $this->checkExitCode($process);
    }

    protected function pullRepo(): void
    {
        $process = $this->process ?? new Process(['git', '-C', $this->getDir(), 'pull', '--progress']);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
        $this->checkExitCode($process);
    }

    protected function checkExitCode(Process $process): void
    {
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
