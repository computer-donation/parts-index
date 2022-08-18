<?php

namespace App\Tests\Command;

use App\Entity\Cpu;
use App\Enum\Cpu\Vendor;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class IndexCpuCommandTest extends KernelTestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->initDatabase();
    }

    public function testExecute()
    {
        $application = new Application(self::$kernel);

        $command = $application->find('app:index-cpu');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Updating cpu repository...', $output);
        $this->assertStringContainsString('Indexing cpus for vendor AMD...', $output);
        $this->assertStringContainsString('Indexing cpus for vendor Intel...', $output);
        $this->assertStringContainsString('Indexed all cpus!', $output);

        $this->assertCpu(
            'amd-23-1-1-ryzen-3-pro-1300-quad-core',
            Vendor::AMD,
            'Ryzen 3 PRO 1300 Quad-Core',
            '98D52182A0'
        );
        $this->assertCpu(
            'amd-6-37-2-core-i5-650',
            Vendor::INTEL,
            'Core i5 650',
            '0BB0F8B0AC'
        );
    }
    
    public function assertCpu(string $id, Vendor $vendor, string $model, string $probe)
    {
        $cpu = $this->entityManager
            ->getRepository(Cpu::class)
            ->find($id)
        ;
        $cpus = $this->entityManager
            ->getRepository(Cpu::class)
            ->findAll()
        ;

        $this->assertInstanceOf(Cpu::class, $cpu);
        $this->assertSame($id, $cpu->id);
        $this->assertSame($vendor, $cpu->vendor);
        $this->assertSame($model, $cpu->model);
        $this->assertSame($probe, $cpu->probe);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    private function initDatabase(): void
    {
        $metaData = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->updateSchema($metaData);
    }
}
