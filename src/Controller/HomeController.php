<?php
namespace App\Controller;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class HomeController extends AbstractController
{
    private SortieRepository $sortieRepository;

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
        $search = $request->query->get('search');
        $campus = $request->query->get('campus');
        $dateDebut = $request->query->get('date_debut');
        $dateFin = $request->query->get('date_fin');
        $createdByMe = $request->query->get('created_by_me');
        $showCompleted = $request->query->get('show_completed');
        $inscrit = $request->query->get('inscrit');
        $nonInscrit = $request->query->get('non_inscrit');

        // Récupération de toutes les sorties
        $sorties = $sortieRepository->findAll();

        // Filtrer par nom
        if ($search) {
            $sorties = array_filter($sorties, function ($sortie) use ($search) {
                return stripos($sortie->getNom(), $search) !== false;
            });
        }

        // Filtrer par campus
        if ($campus) {
            $sorties = array_filter($sorties, function ($sortie) use ($campus) {
                return $sortie->getSortieCampus() && $sortie->getSortieCampus()->getNom() === $campus;
            });
        }

        // Filtrer par date
        if ($dateDebut) {
            $dateDebut = \DateTime::createFromFormat('Y-m-d', $dateDebut);
            $sorties = array_filter($sorties, function ($sortie) use ($dateDebut) {
                return $sortie->getDateHeureDebut() >= $dateDebut;
            });
        }

        if ($dateFin) {
            $dateFin = \DateTime::createFromFormat('Y-m-d', $dateFin);
            $sorties = array_filter($sorties, function ($sortie) use ($dateFin) {
                return $sortie->getDateHeureDebut() <= $dateFin;
            });
        }

        // Filtrer par organisateur
        if ($createdByMe) {
            $currentUser = $this->getUser();
            $sorties = array_filter($sorties, function ($sortie) use ($currentUser) {
                return $sortie->getSortieParticipant() && $sortie->getSortieParticipant()->getId() === $currentUser->getId();
            });
        }

        // Filtrer les sorties terminées
        if ($showCompleted) {
            $currentDate = new \DateTime();
            $sorties = array_filter($sorties, function ($sortie) use ($currentDate) {
                return $sortie->getDateLimiteInscription() < $currentDate;
            });
        }

        // Filtrer les sorties où l'utilisateur est inscrit
        if ($inscrit) {
            $currentUser = $this->getUser();
            $sorties = array_filter($sorties, function ($sortie) use ($currentUser) {
                return in_array($currentUser, $sortie->getParticipants()->toArray());
            });
        }

        // Filtrer les sorties où l'utilisateur n'est pas inscrit
        if ($nonInscrit) {
            $currentUser = $this->getUser();
            $sorties = array_filter($sorties, function ($sortie) use ($currentUser) {
                return !in_array($currentUser, $sortie->getParticipants()->toArray());
            });
        }

        return $this->render('home/index.html.twig', [
            'sorties' => $sorties,
        ]);
    }




    #[Route('/sortie/delete/{id}', name: 'sortie_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $csrfToken = $request->request->get('_token');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete' . $id, $csrfToken))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_home');
        }
        $sortie = $entityManager->getRepository(Sortie::class)->find($id);

        if (!$sortie) {
            $this->addFlash('error', 'La sortie n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
        $entityManager->remove($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie supprimée avec succès.');
        return $this->redirectToRoute('app_home');
    }
}
