<?php

namespace App\Controller\Admin;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(SortieCrudController::class)
            ->generateUrl();

        return $this->redirect($url);
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Mon Application Symfony');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToCrud('Sortie', 'fas fa-graduation-cap', Sortie::class);
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
            yield MenuItem::linkToCrud('Participants', 'fas fa-users', Participant::class);
            yield MenuItem::linkToCrud('Campus', 'fas fa-school', Campus::class);
            yield MenuItem::linkToCrud('Etat', 'fas fa-flag', Etat::class);
            yield MenuItem::linkToCrud('Lieu', 'fas fa-map-marker', Lieu::class);
            yield MenuItem::linkToCrud('Ville', 'fas fa-city', Ville::class);
        }
    }


}
