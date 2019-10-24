<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use App\Service\User\UserService;
use App\Service\Vote\VoteService;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var VoteRepository
     */
    private $voteRepository;

    /**
     * UserController constructor.
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $entityManager
     * @param VoteRepository $voteRepository
     */
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager, VoteRepository $voteRepository)
    {
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->voteRepository = $voteRepository;
    }

    /**
     * Using Symfony's Form
     * Create a form in order to create a new User
     * @param $user
     * @return FormInterface
     */
    public function createFormNewUser($user)
    {
        $form = $this->createForm(Usertype::class, $user);
        return $form;
    }

    /**
     * @Rest\Post("/users/add")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @throws \Exception
     */
    public function createNewUser(
        Request $request,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager)
    {
        $userService = new UserService($passwordEncoder, $userRepository, $entityManager);
        $addUser = $userService->addUser($request, $validator);

        return new JsonResponse($this->serializer->serialize($addUser, 'json'), 200, [], true);
    }

    /**
     * @Rest\Delete("/users/delete", name="delete_user")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param VoteService $voteService
     * @param UserInterface $currentUser
     * @return JsonResponse
     */
    public function deleteUser(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, EntityManagerInterface $entityManager, VoteService $voteService, UserInterface $currentUser)
    {
        $userService = new UserService($passwordEncoder, $userRepository, $entityManager);
        $user = $this->getUser();
        //        $userId = $currentUser->getId();
        // $deleteVotes = $voteService->deleteAllVotesForCurrentUser($this->voteRepository);

       // $voteService->deleteAllVotesForCurrentUser($currentUser, $this->voteRepository);


        $removeUser = $userService->removeUser($user);
        //dd($removeUser);
        return new JsonResponse(null, 204, [], true);
    }
}