<?php

namespace App\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

trait RepoTrait
{
    protected ?Process $process;

    protected function updateRepository(string $repo, string $dir, OutputInterface $output): void
    {
        $output->writeln('Updating repository...');
        if (!is_dir($dir)) {
            $this->cloneRepo($repo, $dir);
        } else {
            $this->pullRepo($dir);
        }
    }

    protected function cloneRepo(string $repo, string $dir): void
    {
        $process = $this->process ?? new Process(['git', 'clone', '--progress', $repo, $dir]);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
        $this->checkExitCode($process);
    }

    protected function pullRepo(string $dir): void
    {
        $process = $this->process ?? new Process(['git', '-C', $dir, 'pull', '--progress']);
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
