<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\VoteRepository;
use App\Service\Movie\MovieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;


/**
 * Class MovieController
 * @package App\Controller
 * @param VoteRepository $voteRepository
 * @return JsonResponse
 */
class MovieController extends AbstractController
{
    private $serializer;

    /**
     * MovieController constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Rest\Get("/movies", name="movies_list")
     * @return Response
     */
    public function getAllSpaceMovies()
    {

        // Initialise une nouvelle session cURL et retourne un identifiant de session cURL à utiliser avec les fonctions curl_setopt(), curl_exec() et curl_close().
        $curl = curl_init();
        // Définit une option de transmission pour le gestionnaire de session cURL fournit.
        curl_setopt_array($curl, array(
            // L'URL à récupérer. Vous pouvez aussi choisir cette valeur lors de l'appel à curl_init().
            CURLOPT_URL => "http://www.omdbapi.com/?s=space&apikey=ceda12d7",
            // TRUE pour retourner le transfert en tant que chaîne de caractères de la valeur retournée par curl_exec() au lieu de l'afficher directement.
            CURLOPT_RETURNTRANSFER => true,
            // Le contenu des en-têtes "Accept-Encoding: " et active le décodage de la réponse. Les encodages supportés sont "identity", "deflate" et "gzip". Si une chaîne vide "" est utilisé, un en-tête contenant tous les types d'encodage supportés est envoyé.
            CURLOPT_ENCODING => "",
            // Le nombre maximal de redirections HTTP à suivre. Utilisez cette option avec l'option CURLOPT_FOLLOWLOCATION.
            CURLOPT_MAXREDIRS => 10,
            // Le temps maximum d'exécution de la fonction cURL exprimé en secondes.
            CURLOPT_TIMEOUT => 30,
            // CURL_HTTP_VERSION_NONE (défaut, laisse cURL décider la version à utiliser), CURL_HTTP_VERSION_1_0 (force HTTP/1.0), ou CURL_HTTP_VERSION_1_1 (force HTTP/1.1).
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // Une méthode de requête qui sera utilisée à la place de "GET" ou "HEAD" lors des requêtes HTTP. Cette commande est pratique pour effectuer un "DELETE" ou une autre commande HTTP exotique. Les valeurs valides sont "GET", "POST", "CONNECT" et plus ; i.e. n'entrez pas une requête HTTP complète ici. Par exemple, entrer "GET /index.html HTTP/1.0\r\n\r\n" serait incorrect.
            CURLOPT_CUSTOMREQUEST => "GET",
            // Un tableau de champs d'en-têtes HTTP à définir, au format array('Content-type: text/plain', 'Content-length: 100')
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Cookie: __cfduid=d4ff25bd3f89afba769679b3c0f9bec981570026554",
                "Host: www.omdbapi.com",
                "Postman-Token: 48c37314-822b-49b8-af9b-4679db54704d,c98ca524-6039-44e9-8b19-bb1365593877",
                "User-Agent: PostmanRuntime/7.17.1",
                "cache-control: no-cache"
            ),
        ));

        // Exécute la session cURL fournie.
        $movies_list = curl_exec($curl);
        // Retourne un message clair représentant la dernière erreur cURL.
        $err = curl_error($curl);

        // Ferme une session cURL et libère toutes les ressources réservées. L'identifiant cURL ch est aussi effacé.
        curl_close($curl);

//        if ($err) {
////            echo "cURL Error #:" . $err;
////        } else {
////            echo $movies_list;
//        }

        //dd(json_decode($movies_list));


        $movies_array = json_decode($movies_list, true);
        dd($movies_array[0]);






        // Il faut deserialize la response returnée


//        $movieSer = new MovieService($this->serializer);
//
//        $finaleResponse = $movieSer->deserialise($movies_list);
//
//        return new Response($movies_list);
    }



//    public function getIdOmdbapi(Response $response)
//    {
//        $ = $response->response-> get('I')
//        $idMovie = $this->getAllSpaceMovies($response);
//    }

//    public function callMovieToDB()
//    {
//        $movieService = new AddMovieService
//    }

    /**
     * @Rest\Post("/users/{user_id}/movies", name="voted_movies")
     * @param Request $request
     * @param $entityManager
     * @return mixed
     */
    public function postVotedMovies(Request $request, EntityManagerInterface $entityManager)
    {
        $user_id = $request->get('user_id');
        $movies_id = $request->request->get('movies');


        // Entity Manager get Current User

        // $repository = $this->getDoctrine()->getRepository(Movie::class);

//        $user = new User();
//        $user->getId();

//  A vérifier :
//        $user_idRepository = $entityManager->getRepository('User');
//        $user_id = $user_idRepository->findAll();

        $vote = new Vote();


        foreach($movies_id as $movie_id){
//             1 - Entity Manager get Movie

             $vote = $entityManager->find('Movie', $movie_id);
             $vote->setMovieId(movie);





//  A vérifier :
//            $movies_idRepository = $entityManager->getRepository('Movie');
//            $movies_id = $movies_idRepository->findAll();

                // 1.1 - ATTENTION au cas : film n'existe pas
            // 2 - Associer au film Current User comme voter

            // 3 - Entity Manager merge/persist

           // $entityManager->merge($user_id);
        }

        $vote->setUserID($user_id);
        $vote->setMovieID($movies_id);


        $entityManager->merge(vote);

        // Entity Manager FLUSH
        $entityManager->flush();




        return new Response($movies_id);
    }





    // Persist un film de l'API ext vers la BDD


}
