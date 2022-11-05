<?php

namespace App\Tests\Command;

use App\Graph\GraphHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class CommandTestCase extends KernelTestCase
{
    protected ?EntityManagerInterface $entityManager = null;
    protected ?GraphHelper $graphHelper = null;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->graphHelper = $container->get('test.'.GraphHelper::class);

        $this->initDatabase();
        $this->initGraphDatabase();
    }

    public function testExecute()
    {
        $application = new Application(self::$kernel);

        $command = $application->find($this->getCommand());
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $this->assertOutput($commandTester->getDisplay());
        $this->assertParts();
        $this->assertNodes();
        $this->assertRelationships();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
        $this->graphHelper = null;
    }

    protected function initDatabase(): void
    {
        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metaData);
    }

    protected function initGraphDatabase(): void
    {
        $this->graphHelper->delete();
    }

    abstract protected function getCommand(): string;

    abstract protected function assertOutput(string $output): void;

    abstract protected function assertParts(): void;

    abstract protected function assertNodes(): void;

    abstract protected function assertRelationships(): void;
}
