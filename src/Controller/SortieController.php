<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository): Response
    {
        $user = $this->getUser();

        // Si l'utilisateur est admin, il peut voir toutes les sorties
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $sorties = $sortieRepository->findAll(); // Admin voit toutes les sorties
        } else {
            $sorties = $sortieRepository->findByEtatOuverte();
        }

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'user' => $user, // Passe l'utilisateur connecté au template
        ]);
    }

    #[Route('/{id}', name: 'sortie_show', methods: ['GET'])]
    public function show(int $id, SortieRepository $sortieRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie demandée n\'existe pas.');
        }

        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/sortie/{id}/inscription', name: 'sortie_inscription')]
    public function inscrire(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour vous inscrire à une sortie.');
            return $this->redirectToRoute('app_login');
        }

        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        // Empêcher l'inscription si la sortie est en "En création"
        if ($sortie->getSortieEtat()->getLibelle() === 'En création') {
            $this->addFlash('error', 'Vous ne pouvez pas vous inscrire à une sortie qui est encore en création.');
            return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
        }

        // Vérifie si la sortie est complète
        if ($sortie->getParticipants()->count() >= $sortie->getNbInscriptionMax()) {
            $this->addFlash('error', 'La sortie est complète.');
            return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
        }

        // Vérifie si l'utilisateur est déjà inscrit
        if ($sortie->getParticipants()->contains($user)) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à cette sortie.');
        } else {
            // Inscrit l'utilisateur
            $sortie->addParticipant($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes inscrit à la sortie.');
        }

        return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
    }


    #[Route('/{id}/desinscription', name: 'sortie_desinscription')]
    public function desinscrire(int $id, EntityManagerInterface $entityManager): Response
    {
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }

        $user = $this->getUser();

        if ($sortie->getParticipants()->contains($user)) {
            // Retire l'utilisateur de la sortie
            $sortie->removeParticipant($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous vous êtes désinscrit de la sortie.');
        } else {
            $this->addFlash('error', 'Vous n\'êtes pas inscrit à cette sortie.');
        }

        return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
    }
}
