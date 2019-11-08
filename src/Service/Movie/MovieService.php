<?php


namespace App\Service\Movie;

use App\Entity\Vote;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MovieService
{


    public function addMovie(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {

        $movie= new Vote();
        $movie_title = $request->request->get('movie_title');
        $movie_title->setTitle($movie_title);
        $errors = $validator->validate($movie);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return new Response($errorsString);
        }

        $entityManager->persist($movie);
        $entityManager->flush();

        return new JsonResponse($movie, 'json');
    }
}
