<?php

namespace App\Repository;

use App\Entity\Cpu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CPU>
 *
 * @method Cpu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cpu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cpu[]    findAll()
 * @method Cpu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CpuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cpu::class);
    }

    public function add(Cpu $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
