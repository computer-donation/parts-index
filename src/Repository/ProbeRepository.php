<?php

namespace App\Repository;

use App\Entity\Probe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Probe>
 *
 * @method Probe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Probe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Probe[]    findAll()
 * @method Probe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProbeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Probe::class);
    }

    public function add(Probe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
