<?php

declare(strict_types=1);
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController{
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer):
    Response
    {
        try {
            $email = (new Email())
                ->from('rickos2003@gmail.com')
                ->to('rickos2003@gmail.com')
                ->subject('Test Email via Gmail')
                ->text('Ceci est un email de test envoyé via Symfony et Gmail.');
            $mailer->send($email);
            return new Response('Email envoyé avec succès');
        } catch (TransportExceptionInterface $e) {
            return new Response('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }
    }
}