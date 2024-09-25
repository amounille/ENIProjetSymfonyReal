<?php
namespace App\Controller\Admin;

use App\Entity\Participant;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ParticipantCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return Participant::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            EmailField::new('mail', 'Email'),
            TextField::new('telephone', 'Téléphone'),
            ArrayField::new('roles', 'Rôles'),
            BooleanField::new('actif', 'Actif'),

            TextField::new('motPasse', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setFormTypeOption('required', true),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Participant) {
            // Vérifie si un mot de passe a été fourni
            if ($entityInstance->getMotPasse() !== null) {
                $encodedPassword = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $entityInstance->getMotPasse()
                );
                $entityInstance->setMotPasse($encodedPassword);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Participant) {
            // Vérifie si un nouveau mot de passe a été fourni
            if ($entityInstance->getMotPasse() !== null) {
                $encodedPassword = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $entityInstance->getMotPasse()
                );
                $entityInstance->setMotPasse($encodedPassword);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }
}

