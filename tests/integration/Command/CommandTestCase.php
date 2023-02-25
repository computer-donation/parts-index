<?php

namespace App\Tests\Command;

use App\Service\CsvExport;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use VirtualFileSystem\FileSystem;

abstract class CommandTestCase extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager = null;
    protected ?CsvExport $csvExport = null;
    protected ?FileSystem $fs = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->initDatabase();
        $this->initVirtualFileSystem();
    }

    public function testExecute(): void
    {
        $application = new Application(self::$kernel);

        $command = $application->find($this->getCommand());
        $commandTester = new CommandTester($command);
        $commandTester->execute($this->getCommandInput());

        $commandTester->assertCommandIsSuccessful();

        $this->assertOutput($commandTester->getDisplay());
        $this->assertDatabase();
        $this->assertCsv();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
        $this->csvExport = null;
        $this->fs = null;
    }

    protected function initDatabase(): void
    {
        $container = self::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metaData);
    }

    protected function initVirtualFileSystem(): void
    {
        $container = self::getContainer();
        $this->fs = new FileSystem(); // Keep virtual file system alive during test
        $this->csvExport = $container->get(CsvExport::class);
        $this->csvExport->setCsvExportDir($this->fs->path(DIRECTORY_SEPARATOR));
    }

    abstract protected function getCommand(): string;

    abstract protected function assertOutput(string $output): void;

    abstract protected function assertDatabase(): void;

    abstract protected function assertCsv(): void;

    protected function loadCsv(string $fileName): array
    {
        $rows = [];
        $file = fopen($this->csvExport->getCsvPath($fileName), 'r');

        while (false !== ($row = fgetcsv($file))) {
            $rows[] = $row;
        }

        fclose($file);

        return $rows;
    }

    protected function getCommandInput(): array
    {
        return [];
    }
}
