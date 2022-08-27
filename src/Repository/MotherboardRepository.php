<?php

namespace App\Repository;

use App\Entity\Motherboard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Motherboard>
 *
 * @method Motherboard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motherboard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motherboard[]    findAll()
 * @method Motherboard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotherboardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motherboard::class);
    }

    public function add(Motherboard $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function has(string $id): bool
    {
        return $this->getEntityManager()->getUnitOfWork()->tryGetById($id, Motherboard::class) ||
            $this->count(['id' => $id]);
    }
}
