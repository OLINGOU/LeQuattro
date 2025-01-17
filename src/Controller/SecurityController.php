<?php

namespace App\Controller;

use DateTime;
use App\Entity\Membre;
use App\Form\RegisterFormType;
use App\Repository\MembreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route("/inscription", name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, MembreRepository $repository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $membre = new Membre();

        $form = $this->createForm(RegisterFormType::class, $membre)
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $membre->setCreatedAt(new DateTime());
            $membre->setUpdatedAt(new DateTime());

            $membre->setRoles(['ROLE_USER']);

            $membre->setPassword(
                $passwordHasher->hashPassword($membre, $membre->getPassword())
            );

            $repository->add($membre, true);

            $this->addFlash('success', 'Votre inscription a été effectuée avec succès, Bienvenue !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
