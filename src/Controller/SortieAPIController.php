<?php
namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieAPIController extends AbstractController
{
    private SortieRepository $sortieRepository;

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    #[Route('/api/sorties', name: 'api_sorties', methods: ['GET'])]
    public function getSorties(Request $request): JsonResponse
    {
        $etat = $request->query->get('etat');
        $dateDebut = $request->query->get('date_debut');
        $dateFin = $request->query->get('date_fin');

        // Récupération des sorties
        $sorties = $this->sortieRepository->findAll();

        // Filtrer par état
        if ($etat) {
            $sorties = array_filter($sorties, function ($sortie) use ($etat) {
                return $sortie->getSortieEtat() && $sortie->getSortieEtat()->getLibelle() === $etat;
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

        // Exclure les sorties en cours de création ou terminées
        $sorties = array_filter($sorties, function ($sortie) {
            $currentDate = new \DateTime();
            $etat = $sortie->getSortieEtat()->getLibelle();
            return $etat !== 'En création' && $etat !== 'Terminée' && $sortie->getDateLimiteInscription() >= $currentDate;
        });

        // Convertir les sorties en JSON
        $data = array_map(function ($sortie) {
            return [
                'nom' => $sortie->getNom(),
                'date_heure_debut' => $sortie->getDateHeureDebut()->format('Y-m-d H:i'),
                'etat' => $sortie->getSortieEtat()->getLibelle(),
                'campus' => $sortie->getSortieCampus()->getNom(),
                'organisateur' => $sortie->getSortieParticipant()->getNom(),
            ];
        }, $sorties);

        return new JsonResponse($data, 200);
    }
}

