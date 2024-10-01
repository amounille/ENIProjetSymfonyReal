<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    #[Required]
    public function setUserPasswordHasherInterface(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // VILLE
        $rennes = new Ville();
        $rennes->setNom('Rennes');
        $rennes->setCodePostal('35000');
        $nantes = new Ville();
        $nantes->setNom('Nantes');
        $nantes->setCodePostal('44000');
        $brest = new Ville();
        $brest->setNom('Brest');
        $brest->setCodePostal('29200');
        $quimper = new Ville();
        $quimper->setNom('Quimper');
        $quimper->setCodePostal('29000');
        $niort = new Ville();
        $niort->setNom('Niort');
        $niort->setCodePostal('79000');

        $manager->persist($rennes);
        $manager->persist($nantes);
        $manager->persist($brest);
        $manager->persist($quimper);
        $manager->persist($niort);

        // CAMPUS
        $eniCampusRennes = new Campus();
        $eniCampusRennes->setNom('ENI Campus Rennes');
        $eniCampusNantes = new Campus();
        $eniCampusNantes->setNom('ENI Campus Nantes');
        $eniCampusBrest = new Campus();
        $eniCampusBrest->setNom('ENI Campus Brest');
        $eniCampusQuimper = new Campus();
        $eniCampusQuimper->setNom('ENI Campus Quimper');
        $eniCampusNiort = new Campus();
        $eniCampusNiort->setNom('ENI Campus Niort');

        $manager->persist($eniCampusRennes);
        $manager->persist($eniCampusNantes);
        $manager->persist($eniCampusBrest);
        $manager->persist($eniCampusQuimper);
        $manager->persist($eniCampusNiort);

        // ETAT
        $ouverte = new Etat();
        $ouverte->setLibelle('Ouverte');
        $enCreation = new Etat();
        $enCreation->setLibelle('En création');
        $cloturee = new Etat();
        $cloturee->setLibelle('Clôturée');
        $enCours = new Etat();
        $enCours->setLibelle('En cours');
        $terminee = new Etat();
        $terminee->setLibelle('Terminée');
        $annulee = new Etat();
        $annulee->setLibelle('Annulée');
        $historisee = new Etat();
        $historisee->setLibelle('Historisée');

        $manager->persist($ouverte);
        $manager->persist($enCreation);
        $manager->persist($cloturee);
        $manager->persist($enCours);
        $manager->persist($terminee);
        $manager->persist($annulee);
        $manager->persist($historisee);


        // USER
        $ludo = new Participant();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $ludo,
            'test'
        );
        $ludo->setRoles(["ROLE_ADMIN"]);
        $ludo->setMail('ludovic.proux@outlook.fr');
        $ludo->setMotPasse($hashedPassword);
        $ludo->setNom('PROUX');
        $ludo->setPrenom('Ludovic');
        $ludo->setTelephone('0606060606');
        $ludo->setActif(true);
        $ludo->setParticipantCampus($eniCampusNiort);

        $rick = new Participant();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $rick,
            'test'
        );
        $rick->setRoles([""]);
        $rick->setMail('rick.bouyaghi@outlook.fr');
        $rick->setMotPasse($hashedPassword);
        $rick->setNom('BOUYAGHI');
        $rick->setPrenom('Rick');
        $rick->setTelephone('0606060606');
        $rick->setActif(true);
        $rick->setParticipantCampus($eniCampusNiort);

        $theo = new Participant();
        $hashedPassword = $this->passwordHasher->hashPassword(
            $theo,
            'test'
        );
        $theo->setRoles([""]);
        $theo->setMail('theo.pohin@outlook.fr');
        $theo->setMotPasse($hashedPassword);
        $theo->setNom('POHIN');
        $theo->setPrenom('Theo');
        $theo->setTelephone('0606060606');
        $theo->setActif(true);
        $theo->setParticipantCampus($eniCampusNiort);


        $manager->persist($ludo);
        $manager->persist($rick);
        $manager->persist($theo);


        // LIEU        
        $expositionNantes = new Lieu();
        $expositionNantes->setNom('Expo Atlantique');
        $expositionNantes->setRue('Avenue des marins');
        $expositionNantes->setLatitude(47.259509);
        $expositionNantes->setLongitude(-1.530581);
        $expositionNantes->setLieuVille($nantes);

        $barBrest = new Lieu();
        $barBrest->setNom('Les dockers');
        $barBrest->setRue('Rue des marchands');
        $barBrest->setLatitude(48.392781);
        $barBrest->setLongitude(-4.482551);
        $barBrest->setLieuVille($brest);

        $restaurantQuimper = new Lieu();
        $restaurantQuimper->setNom('La galette');
        $restaurantQuimper->setRue('Rue Charles de Gaulle');
        $restaurantQuimper->setLatitude(47.996655);
        $restaurantQuimper->setLongitude(-4.102832);
        $restaurantQuimper->setLieuVille($quimper);

        $cinemaRennes = new Lieu();
        $cinemaRennes->setNom('CGR centre');
        $cinemaRennes->setRue('Avenue du centre ville');
        $cinemaRennes->setLatitude(48.110250);
        $cinemaRennes->setLongitude(-1.677116);
        $cinemaRennes->setLieuVille($rennes);

        $salleNiort = new Lieu();
        $salleNiort->setNom('Salle polyvalente');
        $salleNiort->setRue('Rue des associations');
        $salleNiort->setLatitude(46.323521);
        $salleNiort->setLongitude(-0.472900);
        $salleNiort->setLieuVille($niort);

        $expositionQuimper = new Lieu();
        $expositionQuimper->setNom('Expo 4C0DE');
        $expositionQuimper->setRue('Avenue de la gare');
        $expositionQuimper->setLatitude(47.993960);
        $expositionQuimper->setLongitude(-4.091400);
        $expositionQuimper->setLieuVille($quimper);

        $barNiort = new Lieu();
        $barNiort->setNom('La cervoiserie');
        $barNiort->setRue('Rue des marchands');
        $barNiort->setLatitude(46.322658);
        $barNiort->setLongitude(-0.439916);
        $barNiort->setLieuVille($niort);

        $restaurantRennes = new Lieu();
        $restaurantRennes->setNom('La pasta');
        $restaurantRennes->setRue('Rue des commerces');
        $restaurantRennes->setLatitude(48.116105);
        $restaurantRennes->setLongitude(-1.675230);
        $restaurantRennes->setLieuVille($rennes);

        $cinemaBrest = new Lieu();
        $cinemaBrest->setNom('La pellicule');
        $cinemaBrest->setRue('Avenue du cinéma');
        $cinemaBrest->setLatitude(48.386783);
        $cinemaBrest->setLongitude(-4.470140);
        $cinemaBrest->setLieuVille($brest);

        $salleNantes = new Lieu();
        $salleNantes->setNom('Zénith de Nantes');
        $salleNantes->setRue('Avenue des tulipes');
        $salleNantes->setLatitude(47.228582);
        $salleNantes->setLongitude(-1.629048);
        $salleNantes->setLieuVille($nantes);

        $manager->persist($expositionNantes);
        $manager->persist($barBrest);
        $manager->persist($restaurantQuimper);
        $manager->persist($cinemaRennes);
        $manager->persist($salleNiort);
        $manager->persist($expositionQuimper);
        $manager->persist($barNiort);
        $manager->persist($restaurantRennes);
        $manager->persist($cinemaBrest);
        $manager->persist($salleNantes);


        // SORTIE
        $sortieSalleNiort1 = new Sortie();
        $sortieSalleNiort1->setNom('Soirée entre étudiants');
        $sortieSalleNiort1->setDateheureDebut(new \DateTimeImmutable('2024-10-03 18:00:00'));
        $sortieSalleNiort1->setDuree(new \DateTimeImmutable('2000-01-01 01:00:00'));
        $sortieSalleNiort1->setDateLimiteInscription(new \DateTimeImmutable('2024-09-27 17:00:00'));
        $sortieSalleNiort1->setNbInscriptionMax(2);
        $sortieSalleNiort1->setInfosSortie('Première soirée de la rentrée pour rencontrer les étudiants');
        $sortieSalleNiort1->setSortieEtat($ouverte);
        $sortieSalleNiort1->setSortieParticipant($ludo);
        $sortieSalleNiort1->setSortieCampus($eniCampusNantes);
        $sortieSalleNiort1->setSortieLieu($salleNiort);
        $sortieSalleNiort1->addParticipant($ludo);
        $sortieBarNiort1 = new Sortie();
        $sortieBarNiort1->setNom('Soirée entre promos');
        $sortieBarNiort1->setDateheureDebut(new \DateTimeImmutable('2024-10-03 19:00:00'));
        $sortieBarNiort1->setDuree(new \DateTimeImmutable('2000-01-01 01:30:00'));
        $sortieBarNiort1->setDateLimiteInscription(new \DateTimeImmutable('2024-10-01 17:00:00'));
        $sortieBarNiort1->setNbInscriptionMax(30);
        $sortieBarNiort1->setInfosSortie('Soirée pour discuter entre collègues de promos');
        $sortieBarNiort1->setSortieEtat($ouverte);
        $sortieBarNiort1->setSortieParticipant($theo);
        $sortieBarNiort1->setSortieCampus($eniCampusNiort);
        $sortieBarNiort1->setSortieLieu($barNiort);
        $sortieBarNiort1->addParticipant($theo);
        $sortieBarNiort1->addParticipant($ludo);
        $sortieSalleNiort2 = new Sortie();
        $sortieSalleNiort2->setNom('Soirée de Noël');
        $sortieSalleNiort2->setDateheureDebut(new \DateTimeImmutable('2024-12-19 19:00:00'));
        $sortieSalleNiort2->setDuree(new \DateTimeImmutable('2000-01-01 02:00:00'));
        $sortieSalleNiort2->setDateLimiteInscription(new \DateTimeImmutable('2024-12-13 17:00:00'));
        $sortieSalleNiort2->setNbInscriptionMax(25);
        $sortieSalleNiort2->setInfosSortie('Soirée pour fêter Noël avec les étudiants du campus');
        $sortieSalleNiort2->setSortieEtat($ouverte);
        $sortieSalleNiort2->setSortieParticipant($rick);
        $sortieSalleNiort2->setSortieCampus($eniCampusNiort);
        $sortieSalleNiort2->setSortieLieu($salleNiort);
        $sortieSalleNiort2->addParticipant($rick);
        $sortieSalleNiort2->addParticipant($theo);
        $sortieBarNiort2 = new Sortie();
        $sortieBarNiort2->setNom('Soirée projet');
        $sortieBarNiort2->setDateheureDebut(new \DateTimeImmutable('2024-12-19 19:00:00'));
        $sortieBarNiort2->setDuree(new \DateTimeImmutable('2000-01-01 02:00:00'));
        $sortieBarNiort2->setDateLimiteInscription(new \DateTimeImmutable('2024-12-13 17:00:00'));
        $sortieBarNiort2->setNbInscriptionMax(25);
        $sortieBarNiort2->setInfosSortie('Soirée pour l\'organisation du projet de la promo');
        $sortieBarNiort2->setSortieEtat($enCreation);
        $sortieBarNiort2->setSortieParticipant($theo);
        $sortieBarNiort2->setSortieCampus($eniCampusNiort);
        $sortieBarNiort2->setSortieLieu($barNiort);

        $manager->persist($sortieSalleNiort1);
        $manager->persist($sortieBarNiort1);
        $manager->persist($sortieSalleNiort2);
        $manager->persist($sortieBarNiort2);

        $manager->flush();
    }
}
