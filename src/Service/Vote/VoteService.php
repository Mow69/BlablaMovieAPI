<?php


namespace App\Service\Vote;

use App\Entity\Vote;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;


class VoteService
{
    public function voteAction(EntityManagerInterface $entityManager)
    {
        $vote = new Vote();

        $vote->setDate(new \Datetime("2017-03-03T09:00:00Z"));

        $movieRepo = $entityManager->getRepository(Movie::class);
        $movie_id = $movieRepo->find(id);

        $userRepo = $entityManager->getRepository(User::class);
        $user_id = $userRepo->find(id);

        $vote->setUser($user_id);
        $vote->setMovie($movie_id);

        $entityManager->persist($vote);
        $entityManager->flush();

        return $vote;
    }
}