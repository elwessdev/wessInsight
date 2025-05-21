<?php

namespace App\Repository;

use App\Entity\Cvs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Cvs>
 *
 * @method Cvs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cvs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cvs[]    findAll()
 * @method Cvs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CvsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cvs::class);
    }

    public function save(Cvs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cvs $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.userId = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }
}