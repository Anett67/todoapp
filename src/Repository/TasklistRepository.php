<?php

namespace App\Repository;

use App\Entity\Tasklist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tasklist>
 *
 * @method Tasklist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasklist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasklist[]    findAll()
 * @method Tasklist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasklistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tasklist::class);
    }

    public function add(Tasklist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tasklist $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Tasklist[] Returns an array of Tasklist objects
     */
    public function findUsersActiveLists($value): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :val')
            ->setParameter('val', $value)
            ->andWhere('t.archivedAt IS NULL')
            ->orderBy('t.updatedAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Tasklist[] Returns an array of Tasklist objects
     */
    public function findUsersArchivedLists($value): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :val')
            ->setParameter('val', $value)
            ->andWhere('t.archivedAt IS NOT NULL')
            ->orderBy('t.updatedAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

//    public function findOneBySomeField($value): ?Tasklist
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
