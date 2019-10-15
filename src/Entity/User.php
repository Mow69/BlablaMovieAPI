<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    const SERIALIZE_SELF = "User::GROUP_SELF";
    const SERIALIZE_VOTES = "User::GROUP_VOTES";
//      Vérifie que l'adresse email entrée est bien valide (existe bien)
//    public static function loadValidatorMetadata(ClassMetadata $metadata)
//    {
//        $metadata->addPropertyConstraint('mail', new Assert\Valid());
//    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"User::GROUP_SELF"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank(message="The login must be defined.")
     * @Assert\NotNull(message="The login can't be null.")
     * @Assert\Type(
     *     "string",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\Length(
     *      min = 3,
     *      max = 18,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\Regex("#[A-Z0-9]#")
     * @Groups({"User::GROUP_SELF"})
     */
    private $login;

    /**
     * @ORM\Column(type="json")
     * @Groups({"User::GROUP_SELF"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="The password must be defined.")
     * @Assert\NotNull(message="The password can't be null.")
     * @Assert\Type(
     *     "string",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\Length(
     *      min = 12,
     *      minMessage = "Your first name must be at least {{ limit }} characters long"
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="The email must be defined.")
     * @Assert\NotNull(message="The email can't be null.")
     * @Assert\Email(message="The email '{{ value }}' is not a valid email.")
     * @Groups({"User::GROUP_SELF"})
     */
    private $mail;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="The date of birth must be defined.")
     * @Assert\NotNull(message="The date of birth can't be null.")
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     * @Groups({"User::GROUP_SELF"})
     */
    private $birth_date;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="The date of inscription must be defined.")
     * @Assert\NotNull(message="The date of inscription can't be null.")
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     * @Assert\GreaterThanOrEqual("-16 years")
     * @Groups({"User::GROUP_SELF"})
     */
    private $inscription_date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="voter", orphanRemoval=true)
     * @Groups({"User::GROUP_VOTES"})
     */
    private $votes;



    public function __construct() {
        $this->movies = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->mail;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getInscriptionDate(): ?\DateTimeInterface
    {
        return $this->inscription_date;
    }

    public function setInscriptionDate(\DateTimeInterface $inscription_date): self
    {
        $this->inscription_date = $inscription_date;

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setVoter($this);
        }

        return $this;
    }

    /**
     * @param Vote $vote
     * @return $this
     */
    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getVoter() === $this) {
                $vote->setVoter(null);
            }
        }

        return $this;
    }

}
