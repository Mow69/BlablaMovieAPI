<?php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Credentials
{

    /**
     * @Assert\NotBlank(message="The login must be defined.")
     * @Assert\NotNull(message="The login can't be null.")
     * @Assert\Type(
     *     "string",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $login;

    /**
     * @Assert\NotBlank(message="The login must be defined.")
     * @Assert\NotNull(message="The login can't be null.")
     * @Assert\Type(
     *     "string",
     *     message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $password;

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }
}