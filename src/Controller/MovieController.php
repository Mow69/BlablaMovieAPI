<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Vote;
use App\Service\OmdbApiService;
use App\Service\Vote\VoteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;



class MovieController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

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
        return new JsonResponse($moviesData, 200, [] , true);
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
        $movieVote = $entityManager->getRepository('App\Entity\Vote')->findOneBy(['voter'=> $connectedUser,'movie_id'=>$imdbID]);
        // if not empty, movie vote already exists, SO throw error
        if(!empty($movieVote)){
            return new JsonResponse(
                'Action non autorisée, vous avez déjà voté pour CE film',
                405,
                [],
                true
            );
        }

        // Check if number of votes < 3
        if(count($votes) < 3){
            // Check if movie exists in API Externs
            $ombdApiService = new OmdbApiService();
            $isMovieExist = $ombdApiService->checkIfAMovieExistsById($imdbID);
            if($isMovieExist){
                $voteService = new VoteService($request, $validator);
                $vote = $voteService->addVote($entityManager, $connectedUser, $imdbID);

                return new JsonResponse(
                    $this->serializer->serialize(
                        $vote,
                        "json",
                        [
                            "groups"=>[
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


    public function removeVote()
    {
        $voteService = new VoteService();
        $vote = $this->getAllVotedMovies();

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
