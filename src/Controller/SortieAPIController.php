<?php
namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SortieAPIController extends AbstractController
{
    private SortieRepository $sortieRepository;

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    #[Route('/api/sorties', name: 'api_sorties', methods: ['GET'])]
    public function getSorties(): JsonResponse
    {
        // Récupérer toutes les sorties
        $sorties = $this->sortieRepository->findAll();

        // Filtrer les sorties pour ne garder que celles avec l'état "Ouverte"
        $sortiesOuvertes = array_filter($sorties, function ($sortie) {
            return $sortie->getSortieEtat()->getLibelle() === 'Ouverte';
        });

        // Convertir les sorties filtrées en JSON
        $data = array_map(function ($sortie) {
            return [
                'nom' => $sortie->getNom(),
                'date_heure_debut' => $sortie->getDateHeureDebut()->format('Y-m-d H:i'),
                'etat' => $sortie->getSortieEtat()->getLibelle(),
                'campus' => $sortie->getSortieCampus()->getNom(),
                'organisateur' => $sortie->getSortieParticipant()->getNom(),
            ];
        }, $sortiesOuvertes);

        return new JsonResponse($data, 200);
    }
}
