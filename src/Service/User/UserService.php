<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserService
 * @package App\Service\User
 */
class UserService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return User|string
     * @throws \Exception
     */
    public function addUser(Request $request, ValidatorInterface $validator)
    {
        $user = new User();

        //Here, without Symfony's Form (see HOC2019_GIFTS-LB for Symfony Form Use)
        $login = $request->request->get('login');
        $user->setLogin($login);

        $password = $request->request->get('password');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $password
        ));

        $mail = $request->request->get('mail');
        $user->setMail($mail);

        $roles = $user->getRoles();
        $user->setRoles($roles);

        $birthDate = new DateTime($request->request->get('birth_date'));
        $user->setBirthDate($birthDate);

        $user->setInscriptionDate(new DateTime());

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a ConstraintViolationList object. This gives us a nice string for debugging.
             */
            $errorsString = (string)$errors;

            return $errorsString;
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param $id
     * @return User|User[]
     */
    public function removeUser($id)
    {
        $connectedUser = $this->userRepository->find($id);

        $this->entityManager->remove($connectedUser);
        $this->entityManager->flush();

        return $connectedUser;
    }


}
