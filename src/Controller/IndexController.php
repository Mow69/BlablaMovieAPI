<?php

namespace App\Controller;

use App\Service\Vote\VoteService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractController
{
    /**
     * @Rest\Get("/", name="accueil")
     */
    public function accueilDoc()
    {
        return new Response('Bienvenue sur BlablaMovie API !');
    }

    // Persist un vote

    /**
     * @Rest\Post("/movies/vote", name="voted_movies")
     * @param EntityManagerInterface $entityManager
     * @return \App\Entity\Vote
     */
    public function postVote(EntityManagerInterface $entityManager)
    {
        $vote = new VoteService();
        $voter = $vote->voteAction($entityManager);
        return new Response($voter);
    }
}