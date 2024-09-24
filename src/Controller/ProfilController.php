<?php

declare(strict_types=1);
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/profile')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function showProfile(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            // Rediriger vers la page de login si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }
}
