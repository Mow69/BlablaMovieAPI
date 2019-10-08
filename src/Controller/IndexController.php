<?php

namespace App\Controller;

use App\Service\Vote\AddVoteService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IndexController extends AbstractController
{
    /**
     * @Rest\Get("/", name="accueil")
     */
    public function accueilDoc()
    {
        return new Response('Bienvenue sur BlablaMovie API ! </body>');
    }

    // Persist un vote

    /**
     * @Rest\Post("/movies/vote", name="voted_movies")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws \Exception
     */
    public function postVote(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator)
    {
        $voteService = new AddVoteService($request, $validator);
        $vote = $voteService->addVote($entityManager, $this->getUser());
        return new JsonResponse($vote, 'json');
    }
}