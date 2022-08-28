<?php

namespace App\Repository;

use App\Entity\EthernetPciCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EthernetPciCard>
 *
 * @method EthernetPciCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method EthernetPciCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method EthernetPciCard[]    findAll()
 * @method EthernetPciCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EthernetPciCardRepository extends ServiceEntityRepository
{
    use FlushTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EthernetPciCard::class);
    }

    public function add(EthernetPciCard $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function has(string $id): bool
    {
        return $this->getEntityManager()->getUnitOfWork()->tryGetById($id, EthernetPciCard::class) ||
            $this->count(['id' => $id]);
    }
}
