<?php
//
//namespace App\Repository;
//
//use App\Entity\Movie;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
//use Doctrine\Common\Persistence\ManagerRegistry;
//use Doctrine\ORM\Tools\Pagination\Paginator;
//use InvalidArgumentException;
//use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
//
///**
// * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
// * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
// * @method Movie[]    findAll()
// * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
// */
//class MovieRepository extends ServiceEntityRepository
//{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Movie::class);
//    }
//
//    public function findAllPagineEtTrie($page, $nbMaxParPage)
//    {
//        if (!is_numeric($page)) {
//            throw new InvalidArgumentException(
//                'La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').'
//            );
//        }
//
//        if ($page < 1) {
//            throw new NotFoundHttpException('La page demandée n\'existe pas');
//        }
//
//        if (!is_numeric($nbMaxParPage)) {
//            throw new InvalidArgumentException(
//                'La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $nbMaxParPage . ').'
//            );
//        }
//
//        $qb = $this->createQueryBuilder('a')
//            ->where('CURRENT_DATE() >= a.datePublication')
//            ->orderBy('a.datePublication', 'DESC');
//
//        $query = $qb->getQuery();
//
//        $premierResultat = ($page - 1) * $nbMaxParPage;
//        $query->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
//        $paginator = new Paginator($query);
//
//        if ( ($paginator->count() <= $premierResultat) && $page != 1) {
//            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
//        }
//
//        return $paginator;
//    }
//
//
////    /**
////     * @param $id
////     * @return User[] Returns an array of User objects
////     */
////
////    public function findUser($id)
////    {
////        return $this->createQueryBuilder('u')
////            ->andWhere('u.id = :id')
////            ->setParameter('id', $id)
////            ->orderBy('u.id', 'ASC')
////            ->setMaxResults(10)
////            ->getQuery()
////            ->getResult()
////        ;
////    }
//
//
//    /*
//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
//    */
//}
