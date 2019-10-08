<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $movie_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $vote_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMovieId(): ?string
    {
        return $this->movie_id;
    }

    public function setMovieId(string $movie): self
    {
        $this->movie_id = $movie;

        return $this;
    }

    public function getVoteDate(): ?\DateTimeInterface
    {
        return $this->vote_date;
    }

    public function setVoteDate(\DateTimeInterface $vote_date): self
    {
        $this->vote_date = $vote_date;

        return $this;
    }
}
