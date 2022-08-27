<?php

namespace App\Repository;

use App\Entity\Computer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Computer>
 *
 * @method Computer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Computer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Computer[]    findAll()
 * @method Computer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComputerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Computer::class);
    }

    public function add(Computer $entity): void
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
        return $this->getEntityManager()->getUnitOfWork()->tryGetById($id, Computer::class) ||
            $this->count(['id' => $id]);
    }

    public function reference(string $id): ?Computer
    {
        return $this->getEntityManager()->getReference(Computer::class, $id);
    }
}
