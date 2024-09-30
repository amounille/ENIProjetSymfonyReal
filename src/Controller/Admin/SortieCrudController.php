<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use App\Entity\Etat;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SortieCrudController extends AbstractCrudController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom', 'Nom de la sortie'),
            DateTimeField::new('dateHeureDebut', 'Date et heure de début'),
            TimeField::new('duree', 'Durée'),
            DateTimeField::new('dateLimiteInscription', 'Date limite d\'inscription'),
            IntegerField::new('nbInscriptionMax', 'Nombre maximal d\'inscriptions'),
            TextEditorField::new('infosSortie', 'Informations sur la sortie'),

            AssociationField::new('sortieEtat', 'État')
                ->setRequired(true)
                ->setHelp('Choisissez l\'état de la sortie : "En création" ou "Ouverte"'),

            AssociationField::new('sortieLieu', 'Lieu'),
            AssociationField::new('sortieCampus', 'Campus'),
            AssociationField::new('sortieParticipant', 'Participant organisateur')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(function ($entityInstance) {
                    return $entityInstance->getSortieParticipant() === $this->getUser() || $this->security->isGranted('ROLE_ADMIN');
                });
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->displayIf(function ($entityInstance) {
                    return $entityInstance->getSortieParticipant() === $this->getUser() || $this->security->isGranted('ROLE_ADMIN');
                });
            })
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    // Vérification de l'utilisateur pour autoriser la suppression
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Sortie) {
            if ($entityInstance->getSortieParticipant() !== $this->getUser() && !$this->security->isGranted('ROLE_ADMIN')) {
                throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette sortie.');
            }
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }
}
