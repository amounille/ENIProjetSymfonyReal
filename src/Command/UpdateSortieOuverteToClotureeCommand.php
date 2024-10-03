<?php

namespace App\Command;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:update-sortie:ouverte-cloturee', description:'Update Etat ouverte-cloturee from Sorties')]
class UpdateSortieOuverteToClotureeCommand extends Command
{
    public function __construct(private SortieRepository $sortieRepository, private EtatRepository $etatRepository) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get Etat
        $etat = $this->etatRepository->findOneBy(['libelle' => 'Clôturée']);
        // Get Sortie[]
        $sorties = $this->sortieRepository->findBySortiesOuvertes();
        // Get ids from Sortie[]
        $arrayIds = array_column($sorties,'id');
        // Update the Sortie models
        $this->sortieRepository->updateEtat($etat->getId(),$arrayIds);
            
        $io->success(sprintf('Updated etat ouverte-cloturee from "%d" Sorties.', count($arrayIds)));

        return Command::SUCCESS;
    }
}