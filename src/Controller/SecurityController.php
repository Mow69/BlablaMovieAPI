<?php

namespace App\Controller;

use App\Entity\AuthToken;
use App\Entity\Credentials;
use App\Form\Type\CredentialsType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;

class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * SecurityController constructor.
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Rest\Post("/auth-tokens", name="app_auth_token")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     * @throws \Exception
     */
    public function postAuthTokenAction(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            // A améliorer => changer code erreur http & display error form
            return new JsonResponse($form);
        }

        $em = $this->entityManager;

        $user = $em->getRepository('App:User')
            ->findOneBy(['mail' => $credentials->getLogin()]);

        if (!$user) { // L'utilisateur n'existe pas
            return $this->invalidCredentials();
        }

        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) { // Le mot de passe n'est pas correct
            return $this->invalidCredentials();
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return new JsonResponse(json_decode($this->serializer->serialize($authToken, 'json', ['groups' => 'auth-tokens'])));
    }

    /**
     * @Rest\Delete("/auth-tokens/{id}", name="app_auth_token_delete")
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAuthTokenAction(Request $request)
    {
        $connectedUser = $this->getConnectedUser();
        $authTokenId = $request->get('id');
        $authToken = $this->entityManager->getRepository('App:AuthToken')
            ->findOneBy(['id' => $authTokenId, 'user' => $connectedUser]);

        if (!$authToken) { // Le token n'existe pas
            return $this->actionNotAllowed();
        }

        $this->entityManager->remove($authToken);
        $this->entityManager->flush();

        return new JsonResponse(null, 204);
    }


    //    _    _ _   _ _
    //   | |  | | | (_) |
    //   | |  | | |_ _| |___
    //   | |  | | __| | / __|
    //   | |__| | |_| | \__ \
    //    \____/ \__|_|_|___/
    //
    //

    private function invalidCredentials()
    {
        return new JsonResponse('Invalid credentials', 400, [], true);
    }

    private function actionNotAllowed()
    {
        return new JsonResponse('Action not allowed', 405, [], true);
    }

    private function getConnectedUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }

}
