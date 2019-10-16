<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\OmdbApiService;
use App\Service\Vote\VoteService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class MovieController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var
     */
    private $request;

    /**
     * @var
     */
    private $entityManager;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/movies", name="movies_list")
     * @return JsonResponse
     */
    public function getMoviesList()
    {

        $ombdservice = new OmdbApiService();

        $moviesData = $ombdservice->getAllSpaceMovies();
        return new JsonResponse($moviesData, 200, [], true);
    }


    public function getAMovie()
    {

    }

    public function getAllVotedMovies()
    {

    }


// Fonction qui retourneLeVoteCreeVersLeFront

    /**
     * @Rest\Post("/movies/vote", name="voted_movies")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function voteAction(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator)
    {
        $connectedUser = $this->getUser();
        $votes = $connectedUser->getVotes();
        $imdbID = $request->request->get('imdbID');

        // CHECK IF MOVIE ALREADY VOTED
        //  GET vote by VOTER and by MOVIE
        $movieVote = $entityManager->getRepository('App\Entity\Vote')->findOneBy(['voter' => $connectedUser, 'movie_id' => $imdbID]);
        // if not empty, movie vote already exists, SO throw error
        if (!empty($movieVote)) {
            return new JsonResponse(
                'Action non autorisée, vous avez déjà voté pour CE film',
                405,
                [],
                true
            );
        }

        // Check if number of votes < 3
        if (count($votes) < 3) {
            // Check if movie exists in API Externs
            $ombdApiService = new OmdbApiService();
            $isMovieExist = $ombdApiService->checkIfAMovieExistsById($imdbID);
            if ($isMovieExist) {
                $voteService = new VoteService($validator, $entityManager);
                $vote = $voteService->addVote($connectedUser, $imdbID);

                return new JsonResponse(
                    $this->serializer->serialize(
                        $vote,
                        "json",
                        [
                            "groups" => [
                                Vote::SERIALIZE_SELF,
                                Vote::SERIALIZE_VOTER,
                                User::SERIALIZE_SELF,
                            ]
                        ]
                    ),
                    200,
                    [],
                    true
                );
            }
            // IF MOVIE NOT EXIST
            return new JsonResponse(
                "Action non autorisée, le film n'existe pas",
                405,
                [],
                true
            );
        } else {
            // IF NUMBER OF MOVIES > 3
            return new JsonResponse(
                'Action non autorisée, vous avez déjà voté pour 3 films',
                405,
                [],
                true
            );
        }

    }

    /**
     * @Rest\Delete("/movies/vote-delete", name="delete_vote")
     * @param VoteService $voteService
     * @param Request $request
     * @param VoteRepository $voteRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function removeVote(VoteService $voteService, Request $request, VoteRepository $voteRepository, EntityManagerInterface $entityManager)
    {
        $voteId = $request->headers->get('id');

        $voter = $this->getUser();
        $voterId = $voter->getId();

        // TESTER en retravaillant cette ligne pour l'adapter si àa marche à sécuriser la suppression d'un vote sans toucher le vote d'un autre voter que celui connecté.

        $vote = $voteRepository->findOneVoteByVoteIdAndVoterId($voteId, $voterId);

        //$vote = $entityManager->getRepository('App\Entity\Vote')->findOneBy(['voter' => $voter, 'id' => $voteId]);


        if (is_null($vote)) {
            return new JsonResponse(
                'Suppression du vote impossible',
                401,
                [],
                true
            );
        }

        // $voteService->deleteVote($voteId, $voteRepository);

        // dd($vote);
        $voter->removeVote($vote);
        //  dd($vote);
        $entityManager->flush();


        return new JsonResponse('Vote supprimé', 200, [], true);


//        $voteId = $request->headers->get('id');
//
//        $voteOfCurrentVoter = $voteRepository->findOneVoteByVoteId($voteId);
//        $currentVoter->removeVote($voteOfCurrentVoter);
//
//        $entityManager->flush();
//
//
//        return new JsonResponse(null, 204, [], false);


    }


    // FONCTIONS CREEE JUSTE POUR TESTER DES SERVICES DANS LE CONTROLLER VIA POSTMAN, A EFFACER
//    /**
//     * @Rest\Post("/movies/current-week", name="current-week")
//     * @param VoteService $voteService
//     * @return JsonResponse
//     * @throws \Exception
//     */
//    public function displayWeekNumbers(VoteService $voteService)
//    {
//         $currentWeek = $voteService->currentWeekNum();
//
//       // $nowUtc->setTimezone( new \DateTimeZone( 'Australia/Sydney' ) );
//
//
//        return new JsonResponse($currentWeek);
//    }
//
//    /**
//     * @Rest\Post("/movies/lastmonday", name="lastmonday")
//     * @param VoteService $voteService
//     * @return JsonResponse
//     */
//    public function displayLastMonday(VoteService $voteService)
//    {
//        $currentWeek = $voteService->firstDayOfWeek();
//
//        // $nowUtc->setTimezone( new \DateTimeZone( 'Australia/Sydney' ) );
//
//        return new JsonResponse($currentWeek);
//    }

    /**
     * @Rest\Post("/movies/votes/current_week", name="current_week_votes")
     * @param VoteService $voteService
     * @param VoteRepository $voteRepository
     * @return JsonResponse
     */
    public function checkVotesOfCurrentWeekOnBdd(VoteService $voteService, VoteRepository $voteRepository)
    {
        $firstDay = $voteService->firstDayOfWeek();
        $currentVotes = $voteRepository->findVotesByDateCurrentWeek($firstDay);

        return new JsonResponse(($this->serializer->serialize($currentVotes, "json", [
                "groups" => [
                    Vote::SERIALIZE_SELF,
                    Vote::SERIALIZE_VOTER,
                    User::SERIALIZE_SELF,
                ]
            ]
        )
        ),
            200,
            [],
            true
        );

    }


//    // methode non appelée
//    /**
//     * @Rest\Post("/users/{user}/movies", name="voted_movies")
//     * @param Request $request
//     * @param $entityManager
//     * @return mixed
//     */
//    public function postVotedMovies(Request $request, EntityManagerInterface $entityManager)
//    {
//        $user = $request->get('user');
//        $movies_id = $request->request->get('movies');
//
//        $vote = new Vote();
//
//
//        foreach($movies_id as $movie_id){
//
//             $vote = $entityManager->find('Movie', $movie_id);
//             $vote->setMovieId(movie);
//
//        }
//        $vote->setUserID($user);
//        $vote->setMovieID($movies_id);
//
//        $entityManager->merge(vote);
//        // Entity Manager FLUSH
//        $entityManager->flush();
//        return new Response($movies_id);
//    }

}
