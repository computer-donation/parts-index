<?php

namespace App\Repository;

use App\Entity\Part;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Part>
 *
 * @method Part|null find($id, $lockMode = null, $lockVersion = null)
 * @method Part|null findOneBy(array $criteria, array $orderBy = null)
 * @method Part[]    findAll()
 * @method Part[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartRepository extends ServiceEntityRepository
{
    use FlushTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Part::class);
    }

    public function add(Part $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function has(string $id): bool
    {
        return $this->getEntityManager()->getUnitOfWork()->tryGetById($id, Part::class) ||
            $this->count(['id' => $id]);
    }
}
