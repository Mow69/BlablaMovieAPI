<?php


namespace App\Service\Vote;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Security\LoginFormAuthenticator;


class VoteService
{

    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;



    /**
     * VoteService constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $connectedUser
     * @param $imdbID
     * @return Vote|string
     * @throws \Exception
     */
    public function addVote($connectedUser, $imdbID)
    {
        $vote = new Vote();
        $vote->setVoter($connectedUser);
        $vote->setMovieId($imdbID);
        $vote->setVoteDate(new DateTime());

        $errors = $this->validator->validate($vote);
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return $errorsString;
        }


        $this->entityManager->persist($vote);
        $this->entityManager->flush();

        return $vote;


    }


    public function deleteAllVotesForCurrentUser(UserInterface $currentUser, VoteRepository $voteRepository)
    {
        $currentUserId = $currentUser->getId();

        $getVotesOfCurrentUser = $voteRepository->findByVoterId($currentUserId);

        // dd($getVotesOfCurrentUser);

        foreach ($getVotesOfCurrentUser as $voteItem)
        {
            $this->entityManager->remove($voteItem);
            $this->entityManager->flush();
        }

        return $getVotesOfCurrentUser;

    }

    public function deleteVote()
    {
        
    }


//    public function removeVote($existingVote, EntityManagerInterface $entityManager, ManagerRegistry $registry)
//    {
//        $vote = $existingVote->getVote();
//
//        // $removeUser = $userId->remove($userId);
////  ????????
//        $voteRepo = new VoteRepository($registry);
//
//        $existingVote = $voteRepo->find($vote);
//
//        // dd($userId);
//
//        $entityManager->remove($existingVote);
//        $entityManager->flush();
//
//        return $existingVote;
//        // return $this->redirectToRoute('accueil');

//    }



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