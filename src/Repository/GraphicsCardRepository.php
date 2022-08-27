<?php

namespace App\Repository;

use App\Entity\GraphicsCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GraphicsCard>
 *
 * @method GraphicsCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method GraphicsCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method GraphicsCard[]    findAll()
 * @method GraphicsCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GraphicsCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GraphicsCard::class);
    }

    public function add(GraphicsCard $entity): void
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
        return $this->getEntityManager()->getUnitOfWork()->tryGetById($id, GraphicsCard::class) ||
            $this->count(['id' => $id]);
    }
}
