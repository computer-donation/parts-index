<?php

namespace App\Command;

use App\Entity\Probe;
use App\Repository\ProbeRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

abstract class AbstractIndexCommand extends Command
{
    public function __construct(
        protected ProbeRepository $probeRepository,
        protected Connection $connection,
        protected ?Process $process = null
    ) {
        parent::__construct();
    }

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

    protected function getProbe(string $id): Probe
    {
        if (!$probe = $this->probeRepository->find($id)) {
            $probe = new Probe();
            $probe->id = $id;
            $this->probeRepository->add($probe, true);
        }

        return $probe;
    }

    protected function disableLogging(): void
    {
        $this->connection->getConfiguration()->setSQLLogger(null);
    }
}
