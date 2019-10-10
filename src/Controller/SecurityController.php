<?php

namespace App\Controller;

use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Rest\Post("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return JsonResponse
     */
    public function login(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        // if ($this->getUser()) {
        //    $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error === null) {
            return new JsonResponse($lastUsername);
        }
        return new JsonResponse($error);
    }

    /**
     * @Rest\Get("/logout", name="app_logout")
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }




//    /**
//     * @Rest\Post("/users/delete", name="delete_user")
//     * @param $id
//     * @return RedirectResponse
//     */
//    public function deleteUser()
//    {
//        $user = $this->getUser();
//        $userId = $user->getID();
//
//        $entityManager = $this->getDoctrine()->getManager();
//        $usrRepo = $entityManager->getRepository(User::class);
//
//        $user = $usrRepo->find($id);
//        $entityManager->remove($user);
//        $entityManager->flush();
//
//
//        return  JsonResponse();
//       // return $this->redirectToRoute('accueil');
//
//
//    }

}
