<?php

namespace App\Repository;

use App\Entity\Credentials;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Credentials|null find($id, $lockMode = null, $lockVersion = null)
 * @method Credentials|null findOneBy(array $criteria, array $orderBy = null)
 * @method Credentials[]    findAll()
 * @method Credentials[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CredentialsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Credentials::class);
    }
}
