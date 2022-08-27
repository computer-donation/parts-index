<?php

namespace App\Repository;

use App\Entity\Printer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Printer>
 *
 * @method Printer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Printer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Printer[]    findAll()
 * @method Printer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrinterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Printer::class);
    }

    public function add(Printer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
        }
    }

    public function has(string $id): bool
    {
        return $this->getEntityManager()->getUnitOfWork()->tryGetById($id, Printer::class) ||
            $this->count(['id' => $id]);
    }
}
