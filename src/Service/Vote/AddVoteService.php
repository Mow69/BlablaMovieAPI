<?php


namespace App\Service\Vote;

use App\Entity\User;
use App\Entity\Vote;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AddVoteService
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * AddVoteService constructor.
     * @param Request $request
     * @param ValidatorInterface $validator
     */
    public function __construct(Request $request, ValidatorInterface $validator)
    {
        $this->request = $request;
        $this->validator = $validator;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param $maurice
     * @return Vote|string
     * @throws \Exception
     */
    public function addVote(EntityManagerInterface $entityManager, $maurice)
    {
        $vote = new Vote();

        $movie_id = $this->request->request->get('imdbID');
        $vote->setMovieId($movie_id);


        $vote->setVoteDate(new DateTime());


        // TODO : recuperer l'id de l'user connecté
        $vote->setUser($maurice);



        $errors = $this->validator->validate($vote);
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return $errorsString;
        }
        $entityManager->persist($vote);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $vote;
    }



// methode non appelée
//    public function voteAction(EntityManagerInterface $entityManager)
//    {
//        $vote = new Vote();
//
//        $vote->setVoteDate(new \Datetime("2017-03-03T09:00:00Z"));
//
//        $movieRepo = $entityManager->getRepository(Movie::class);
//        $movie_id = $movieRepo->find(id);
//
//        $userRepo = $entityManager->getRepository(User::class);
//        $user_id = $userRepo->find(id);
//
//        $vote->setUser($user_id);
//        $vote->setMovie($movie_id);
//
//        $entityManager->persist($vote);
//        $entityManager->flush();
//
//        return $vote;
//    }
}