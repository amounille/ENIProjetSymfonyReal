<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use App\Service\ImagesUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\PasswordService;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant')]
class ParticipantController extends AbstractController
{
    // Liste des participants
    #[Route('/', name: 'app_participant_index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    // Création d'un nouveau participant
    #[Route('/new', name: 'app_participant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PasswordService $passwordService, ImagesUploader $imagesUploader): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encodage du mot de passe
            $plainPassword = $form->get('motPasse')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordService->encodePassword($participant, $plainPassword);
                $participant->setMotPasse($hashedPassword);
            }

            /** @var UploadedFile $image */
            $image = $form->get('photo')->getData();
            if ($image) {

                $filesystem = new Filesystem();
                $photo = $imagesUploader->getImagesDirectory().DIRECTORY_SEPARATOR.$participant->getPhoto();
                try {
                    $filesystem->remove($photo);
                } catch (IOExceptionInterface $exception) {
                    echo "An error occurred while creating your directory at ".$exception->getPath();
                }

                $imageFileName = $imagesUploader->upload($image);
                $participant->setPhoto($imageFileName);
            }

            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participant_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $participant = $entityManager->getRepository(Participant::class)->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant non trouvé.');
        }

        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }


    // Éditer un participant
    #[Route('/{id}/edit', name: 'app_participant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager, PasswordService $passwordService, ImagesUploader $imagesUploader): Response
    {
        $participant = $entityManager->getRepository(Participant::class)->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Participant not found');
        }

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour du mot de passe si fourni
            $plainPassword = $form->get('motPasse')->getData();
            if ($plainPassword) {
                $hashedPassword = $passwordService->encodePassword($participant, $plainPassword);
                $participant->setMotPasse($hashedPassword);
            }

            /** @var UploadedFile $image */
            $image = $form->get('photo')->getData();
            if ($image) {

                $filesystem = new Filesystem();
                $photo = $imagesUploader->getImagesDirectory().DIRECTORY_SEPARATOR.$participant->getPhoto();
                try {
                    $filesystem->remove($photo);
                } catch (IOExceptionInterface $exception) {
                    echo "An error occurred while creating your directory at ".$exception->getPath();
                }

                $imageFileName = $imagesUploader->upload($image);
                $participant->setPhoto($imageFileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    // Supprimer un participant
    #[Route('/{id}', name: 'app_participant_delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    }
}
