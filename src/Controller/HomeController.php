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
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');

        if ($search) {
            $sorties = $this->sortieRepository->createQueryBuilder('s')
                ->where('s.nom LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } else {
            $sorties = $this->sortieRepository->findAll();
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
