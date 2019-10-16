<?php


namespace App\Service\Vote;

use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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
     * @var
     */
    private $serializer;


    /**
     * VoteService constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $serializer->serializer = $serializer;
    }

    ////// VOTE Methods :

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

    /**
     * @param UserInterface $currentUser
     * @param VoteRepository $voteRepository
     * @return Vote[]
     */
    public function deleteAllVotesForCurrentUser(UserInterface $currentUser, VoteRepository $voteRepository)
    {
        $currentUserId = $currentUser->getId();
        $getVotesOfCurrentUser = $voteRepository->findByVoterId($currentUserId);

        foreach ($getVotesOfCurrentUser as $voteItem) {
            $this->entityManager->remove($voteItem);
            $this->entityManager->flush();
        }

        return $getVotesOfCurrentUser;
    }


    ////// DATE Methods :

    /**
     * @return false|string
     * @throws \Exception
     */
    public function currentWeekNum()
    {
        $date = new DateTime();
        $formated_date = $date->format('Y-m-d');
        $dateStrToTime = strtotime($formated_date);
        $ourWeekNum = date('W', $dateStrToTime);

        return $ourWeekNum;
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function currentDayNumOfWeek()
    {
        $date = new DateTime();
        $formated_date = $date->format('Y-m-d');
        $dateStrToTime = strtotime($formated_date);
        $ourDayNumOfWeek = date('w', $dateStrToTime);

        return $ourDayNumOfWeek;
    }

    /**
     * @return false|string
     */
    public function firstDayOfWeek()
    {
        $day = date('w');
        $firstDay = date('Y-m-d', strtotime('+'.(1-$day).'day'));
        // $lastdDay = date('Y-m-d', strtotime('+'.(7-$day).'day'));

        return $firstDay;
    }

    /**
     * @param VoteService $voteService
     * @param VoteRepository $voteRepository
     * @return mixed
     */
    public function checkVotesOfCurrentWeekOnBdd(VoteService $voteService, VoteRepository $voteRepository)
    {
        $firstDay = $voteService->firstDayOfWeek();
        $currentVotes = $voteRepository->findVotesByDateCurrentWeek($firstDay);

        return $currentVotes;
    }
}