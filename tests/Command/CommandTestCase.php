<?php

namespace App\Tests\Command;

use App\Graph\GraphHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use WikibaseSolutions\CypherDSL\Functions\Exists;
use WikibaseSolutions\CypherDSL\Query;

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

    protected function getNode(string $label, string $id, array $properties): array
    {
        $variable = Query::variable('node');
        $node = Query::node($label)
            ->named($variable)
            ->withProperty('id', Query::literal($id));

        $statement = Query::new()
            ->match($node)
            ->returning(array_map(fn (string $property) => $variable->property($property), $properties))
            ->build();

        return $this->graphHelper->query($statement)->getResultSet()[0];
    }

    protected function hasRelationship(string $sourceLabel, string $sourceId, string $targetLabel, string $targetId, string $relationship): bool
    {
        $source = Query::variable('source');
        $sourceNode = Query::node($sourceLabel)->withProperties([
            'id' => Query::literal($sourceId),
        ])->named($source);

        $target = Query::variable('target');
        $targetNode = Query::node($targetLabel)->withProperties([
            'id' => Query::literal($targetId),
        ])->named($target);

        $statement = Query::new()
            ->match($sourceNode)
            ->match($targetNode)
            ->returning((new Exists($source->relationshipTo(Query::node()->named($target), $relationship)))->alias('hasRelationship'))
            ->build();

        return 'true' === $this->graphHelper->query($statement)->getResultSet()[0][0];
    }
}
