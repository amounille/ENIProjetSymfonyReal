<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Dans le RegistrationController
        if ($form->isSubmitted() && $form->isValid()) {
            // Encode le mot de passe avant de l'enregistrer
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setMotPasse($encodedPassword);

            // Récupérer les rôles sélectionnés dans le formulaire
            $roles = $form->get('roles')->getData();

            // Si aucun rôle n'est sélectionné, on attribue 'ROLE_USER' par défaut
            if (empty($roles)) {
                $roles = ['ROLE_USER'];
            }
            $user->setRoles($roles);

            // Assigner la valeur par défaut à 'actif'
            $user->setActif(true);

            // Enregistre le participant
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirection après l'inscription réussie
            return $this->redirectToRoute('app_home');
        }


        return $this->render('registration/registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
