<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FloatField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class LieuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lieu::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setDisabled(),
            TextField::new('nom')->setLabel('Nom du Lieu'),
            TextField::new('rue')->setLabel('Rue'),
            NumberField::new('latitude')->setLabel('Latitude'),
            NumberField::new('longitude')->setLabel('Longitude'),
            AssociationField::new('lieuVille')->setLabel('Ville')->setRequired(true),
        ];
    }
}
