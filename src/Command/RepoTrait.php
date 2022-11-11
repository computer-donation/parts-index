<?php

namespace App\Command;

use App\Tests\Process\VoidProcess;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\Service\Attribute\Required;

trait RepoTrait
{
    protected string $reposDir;
    protected ?Process $process;

    #[Required]
    public function setReposDir(
        #[Autowire('%app.repos_dir%')]
        string $reposDir,
    ): void {
        $this->reposDir = $reposDir;
    }

    #[Required]
    public function setProcess(
        #[Autowire(service: VoidProcess::class)]
        ?Process $process = null
    ): void {
        $this->process = $process;
    }

    protected function getRepoDir(string $repo): string
    {
        return $this->reposDir.DIRECTORY_SEPARATOR.$repo;
    }

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
