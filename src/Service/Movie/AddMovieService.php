<?php


namespace App\Service\Movie;

use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddMovieService
{


    public function addMovie(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        $movie= new Vote();


        $movie_title = $request->request->get('movie_title');
        $movie_title->setTitle($movie_title);


//        $birthDate = new DateTime($request->request->get('birth_date'));
//        $movie->setBirthDate($birthDate);


        $errors = $validator->validate($movie);
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return new Response($errorsString);
        }

        /* you can fetch the EntityManager via $this->getDoctrine()->getManager() or you can add an argument to the action: addUser(EntityManagerInterface $entityManager)
        */
        // tell Doctrine you want to (eventually) save the user (no queries yet)
        $entityManager->persist($movie);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new JsonResponse($movie, 'json');
    }
}
