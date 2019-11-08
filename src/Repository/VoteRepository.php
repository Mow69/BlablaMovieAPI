<?php

namespace App\Repository;

use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @method Vote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vote[]    findAll()
 * @method Vote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }


    /**
     * @param $currentUserId
     * @return Vote[] Returns an array of Vote objects
     */

    public function findByVoterId($currentUserId)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.voter = :val')
            ->setParameter('val', $currentUserId)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $voteId
     * @param $voterId
     * @return Vote|null
     * @throws NonUniqueResultException
     */
    public function findOneVoteByVoteIdAndVoterId($voteId, $voterId): ?Vote
    {
            return $this->createQueryBuilder('v')
                ->andWhere('v.id = :val1', 'v.voter = :val2')
                ->setParameter('val1', $voteId)
                ->setParameter('val2',  $voterId)
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     * @param $date
     * @return mixed
     */
    public function findVotesByDateCurrentWeek($date)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.vote_date >= DATE(:date)')
            ->setParameter('date', $date)
            ->orderBy('v.vote_date', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
