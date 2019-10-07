<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 * @ORM\Table(name="participations",
uniqueConstraints={
@ORM\UniqueConstraint(name="votes", uniqueConstraints={@ORM\UniqueConstraint(name="vote_unique", columns={"user_id", "movie_id"})
}
)
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
     * @ORM\Column(type="string", length=255)
     */
    private $movie_title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $movie_poster;

    /**
     * @ORM\Column(type="datetime")
     */
    private $vote_date;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="votes")
     */
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity=Movie::class)
     */
    private $movie_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovieTitle(): ?string
    {
        return $this->movie_title;
    }

    public function setMovieTitle(string $movie_title): self
    {
        $this->movie_title = $movie_title;

        return $this;
    }

    public function getMoviePoster(): ?string
    {
        return $this->movie_poster;
    }

    public function setMoviePoster(?string $movie_poster): self
    {
        $this->movie_poster = $movie_poster;

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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getMovieId(): ?int
    {
        return $this->movie_id;
    }

    public function setMovieId(int $movie_id): self
    {
        $this->movie_id = $movie_id;

        return $this;
    }
}
