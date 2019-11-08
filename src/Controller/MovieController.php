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


    /**
     * @Rest\Post("/movies/vote", name="voted_movies")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param VoteService $voteService
     * @param VoteRepository $voteRepository
     * @return JsonResponse
     * @throws \Exception
     */
    public function voteAction(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator, VoteService $voteService, VoteRepository $voteRepository)
    {
        $connectedUser = $this->getUser();
        //$votes = $connectedUser->getVotes();
        $imdbID = $request->request->get('imdbID');
        $weekVotes = $voteService->checkVotesOfCurrentWeekOnBdd($voteService, $voteRepository);

        // CHECK IF MOVIE ALREADY VOTED
        //  GET vote by VOTER and by MOVIE
        $sameVoteResult = $entityManager->getRepository('App\Entity\Vote')->findOneBy(['voter' => $connectedUser, 'movie_id' => $imdbID]);
        // if not empty, movie vote already exists, SO throw error
        if (!empty($sameVoteResult)) {
            return new JsonResponse(
                'Action non autorisée, vous avez déjà voté pour CE film',
                405,
                [],
                true
            );
        }

        // Check if number of votes < 3
        if (count($weekVotes) < 3) {
            // Check if movie exists in OmdbAPI
            $ombdApiService = new OmdbApiService();
            $isMovieExist = $ombdApiService->checkIfAMovieExistsById($imdbID);
            if ($isMovieExist) {
                $voteService = new VoteService($validator, $entityManager, $this->serializer);
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
     * @param Request $request
     * @param VoteRepository $voteRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function removeVote(Request $request, VoteRepository $voteRepository, EntityManagerInterface $entityManager)
    {
        $voteId = $request->headers->get('id');
        $voter = $this->getUser();
        $voterId = $voter->getId();
        $vote = $voteRepository->findOneVoteByVoteIdAndVoterId($voteId, $voterId);

        if (is_null($vote)) {
            return new JsonResponse(
                'Suppression du vote impossible',
                401,
                [],
                true
            );
        }

        $voter->removeVote($vote);
        $entityManager->flush();
        return new JsonResponse('Vote supprimé', 200, [], true);
    }
}
