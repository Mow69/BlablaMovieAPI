<?php


namespace App\Service\Vote;

use App\Entity\Vote;
use App\Repository\VoteRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
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
     * VoteService constructor.
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
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

        // dd($getVotesOfCurrentUser);

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
        //$weekNum = count(day);

//        $month = 10;
//        $year = 2019;
//        echo "Week #'s in October 2019: ";
//        $week_num_correction = strftime('%U', mktime(0, 0, 0, 1, 1, $year)) === '00' ? 1 : 0;
//        $week_numbers = range(strftime('%U', mktime(0, 0, 0, $month, 1, $year)) + $week_num_correction, strftime('%U', mktime(0, 0, 0, $month + 1, 0, $year)) + $week_num_correction);
//        foreach ($week_numbers as $week_number) return $week_number;

        //$date = date('W',('19-01-01'));
      //  $nowUtc->setTimezone( new \DateTimeZone( 'Australia/Sydney' ) );

        $date = new DateTime();
        $formated_date = $date->format('Y-m-d');
        $dateStrToTime = strtotime($formated_date);
        $ourWeekNum = date('W', $dateStrToTime);
        $ourDayNumOfWeek = date('w', $dateStrToTime);
        
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

    public function firstDayOfWeek()
    {
//        $date = new DateTime();
//        $formated_date = $date->format('Y-m-d');


        $day = date('w');

        $firstDay = date('Y-m-d', strtotime('+'.(1-$day).'day'));
        $lastdDay = date('Y-m-d', strtotime('+'.(7-$day).'day'));


        return $firstDay;
    }

    public function dayComparaison()
    {
        $firstDayOfWeek = $this->firstDayOfWeek();

        $dateTime = new DateTime();
        $currentDayNumOfWeek = $this->currentDayNumOfWeek();

        $comparaisonDaysNumBtwNowAndMonday = ($firstDayOfWeek - $currentDayNumOfWeek);

        if($comparaisonDaysNumBtwNowAndMonday > 1)
        {

        };

    }


//      Méthode non utilisée car le Controller appelle une méthode du Repository
//    /**
//     * @param int $voteId
//     * @param int $voterId
//     * @param VoteRepository $voteRepository
//     * @return Vote|null
//     * @throws NonUniqueResultException
//     */
//    public function deleteVote(int $voteId, int $voterId, VoteRepository $voteRepository)
//    {
//        $vote = $voteRepository->findOneVoteByVoteIdAndVoterId($voteId, $voterId);
//
//
//        $this->entityManager->remove($vote);
//        $this->entityManager->flush();
//
//        return $vote;
//    }

//    public function getCurrentVotes(Request $request)
//    {
//        $currentVote = $request->request(vote_id);
//    }


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