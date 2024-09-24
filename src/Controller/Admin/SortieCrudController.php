<?php
// src/Controller/Admin/SortieCrudController.php
// src/Controller/Admin/SortieCrudController.php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom', 'Nom de la sortie'),
            DateTimeField::new('dateHeureDebut', 'Date et heure de début'),
            TimeField::new('duree', 'Durée'),
            DateTimeField::new('dateLimiteInscription', 'Date limite d\'inscription'),
            IntegerField::new('nbInscriptionMax', 'Nombre maximal d\'inscriptions'),
            TextEditorField::new('infosSortie', 'Informations sur la sortie'),
            TextField::new('etat', 'État'),
            AssociationField::new('sortieLieu', 'Lieu'),
            AssociationField::new('sortieEtat', 'État'),
            AssociationField::new('sortieCampus', 'Campus'),
            AssociationField::new('sortieParticipant', 'Participant organisateur')
        ];
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

}
