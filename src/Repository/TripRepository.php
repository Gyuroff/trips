<?php

namespace App\Repository;

use App\Entity\Trip;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Trip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trip[]    findAll()
 * @method Trip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    public function isThereOverlaps(DateTime $startDate, DateTime $endDate, ?UserInterface $user): bool
    {

        return count($this->createQueryBuilder('t')
            ->where('t.startDate BETWEEN :startDate AND :endDate')
            ->orWhere('t.endDate BETWEEN :startDate AND :endDate')
            ->orWhere('t.startDate BETWEEN :startDate AND :endDate')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult()) > 0;
    }
}
