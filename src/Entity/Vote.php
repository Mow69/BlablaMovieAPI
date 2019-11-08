<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    const SERIALIZE_SELF = "Vote::GROUP_SELF";
    const SERIALIZE_VOTER = "Vote::GROUP_VOTER";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Vote::GROUP_SELF"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="votes")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"Vote::GROUP_VOTER"})
     */
    private $voter;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"Vote::GROUP_SELF"})
     */
    private $vote_date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Vote::GROUP_SELF"})
     */
    private $movie_id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getVoter(): ?User
    {
        return $this->voter;
    }

    /**
     * @param User|null $voter
     * @return $this
     */
    public function setVoter(?User $voter): self
    {
        $this->voter = $voter;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getVoteDate(): ?\DateTimeInterface
    {
        return $this->vote_date;
    }

    public function setVoteDate(\DateTimeInterface $vote_date): self
    {
        $this->vote_date = $vote_date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMovieId(): ?string
    {
        return $this->movie_id;
    }

    /**
     * @param string $movie_id
     * @return $this
     */
    public function setMovieId(string $movie_id): self
    {
        $this->movie_id = $movie_id;

        return $this;
    }
}
