<?php


namespace App\Service\Vote;

use App\Entity\User;
use App\Entity\Vote;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class VoteService
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
     * VoteService constructor.
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
     * @param $connectedUser
     * @param $imdbID
     * @return Vote|string
     * @throws \Exception
     */
    public function addVote(EntityManagerInterface $entityManager, $connectedUser, $imdbID)
    {
        $vote = new Vote();
        $vote->setVoter($connectedUser);
        $vote->setMovieId($imdbID);
        $vote->setVoteDate(new DateTime());

        $entityManager->persist($vote);
        $entityManager->flush();

        return $vote;

/////// a remettre avant le persist quand j'aurais mis les assets aux attributs de vote entity
        $errors = $this->validator->validate($vote);
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return $errorsString;
        }
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
//        $user = $userRepo->find(id);
//
//        $vote->setUser($user);
//        $vote->setMovie($movie_id);
//
//        $entityManager->persist($vote);
//        $entityManager->flush();
//
//        return $vote;
//    }
}