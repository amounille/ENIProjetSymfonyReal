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

            // Utilisation de l'AssociationField pour l'état
            AssociationField::new('sortieEtat', 'État')
                ->setRequired(true)
                ->setHelp('Choisissez l\'état de la sortie : "En création" ou "Ouverte"'),

            AssociationField::new('sortieLieu', 'Lieu'),
            AssociationField::new('sortieCampus', 'Campus'),
            AssociationField::new('sortieParticipant', 'Participant organisateur')
        ];
    }

    // Ajout d'une logique pour définir l'état par défaut à "En création"
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Sortie) {
            // Si aucun état n'est défini, on attribue l'état par défaut "En création"
            if (!$entityInstance->getSortieEtat()) {
                $etat = $entityManager->getRepository(Etat::class)->findOneBy(['libelle' => 'En création']);
                $entityInstance->setSortieEtat($etat);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    // Vérification de l'utilisateur pour autoriser la suppression
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Sortie) {
            // Vérifier si l'utilisateur connecté est l'organisateur de la sortie
            if ($entityInstance->getSortieParticipant() !== $this->getUser()) {
                // Si l'utilisateur n'est pas l'organisateur, on bloque la suppression
                throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette sortie.');
            }
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // Mise à jour de l'action "delete" pour l'afficher uniquement si l'utilisateur est l'organisateur
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(function ($entityInstance) {
                    // Afficher le bouton supprimer uniquement si l'utilisateur connecté est l'organisateur
                    return $entityInstance->getSortieParticipant() === $this->getUser();
                });
            })
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }
}
