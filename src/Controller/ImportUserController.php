<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ImportUsersType;
use App\Service\ImportUsersService;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ImportUserController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    #[Route('/import', name: 'app_import', methods: ['GET', 'POST'])]
    public function import(Request $request, KernelInterface $kernel, Filesystem $filesystem, UserPasswordHasherInterface $passwordHasher, ImportUsersService $importUsersService, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImportUsersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Get the file
            /** @var UploadedFile $usersCsvFile */
            $usersCsvFile = $form->get('users_csv')->getData();

            // Use the service for the import
            $usersCsvFile->move($kernel->getProjectDir() . '/var/tmp', $usersCsvFile->getFilename());
            $importUsersService->setFileName($kernel->getProjectDir() . '/var/tmp/' . $usersCsvFile->getFilename());
            $importUsersService->extractData();
            $filesystem->remove($importUsersService->getFileName());

            // Insert users
            foreach ($importUsersService->getDataUsers() as $dataUser) {
                $newUser = new Participant();

                // Encode password
                $encodedPassword = $passwordHasher->hashPassword(
                    $newUser,
                    $dataUser[$importUsersService::CELL_PASSWORD]
                );
                $newUser->setMail($dataUser[$importUsersService::CELL_EMAIL]);
                $newUser->setMotPasse($encodedPassword);
                $newUser->setNom($dataUser[$importUsersService::CELL_NAME]);
                $newUser->setPrenom($dataUser[$importUsersService::CELL_FIRSTNAME]);
                $newUser->setTelephone($dataUser[$importUsersService::CELL_PHONENUMBER]);

                // Insert the user
                $entityManager->persist($newUser);
                $entityManager->flush();
            }

            // Affiche un message de succès après l'import
            $this->addFlash('success', 'L\'importation des utilisateurs a été effectuée avec succès !');

        }

        return $this->render('participant/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
