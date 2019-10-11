<?php

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
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
     * @param EntityManagerInterface $entityManager
     * @return User|string
     * @throws \Exception
     */
    public function addUser(Request $request, ValidatorInterface $validator, \Doctrine\ORM\EntityManagerInterface $entityManager)
    {

        $serializer = new CustomUserSerializer();

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

        /* you can fetch the EntityManager via $this->getDoctrine()->getManager() or you can add an argument to the action: addUser(EntityManagerInterface $entityManager)
        */
        // tell Doctrine you want to (eventually) save the user (no queries yet)
        $this->entityManager->persist($user);
        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param $id
     * @return User|User[]
     */
    public function removeUser($id)
    {
        // $removeUser = $userId->remove($userId);
//  ????????

        $connectedUser = $this->userRepository->find($id);

        // dd($userId);

        $this->entityManager->remove($connectedUser);
        $this->entityManager->flush();

        return $connectedUser;
        // return $this->redirectToRoute('accueil');

    }


}
