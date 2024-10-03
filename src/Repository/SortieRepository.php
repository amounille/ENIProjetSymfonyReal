<?php

namespace App\Repository;

use App\Entity\Sortie;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findByNom(string $search)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }
    public function findByEtatOuverte()
    {
        return $this->createQueryBuilder('s')
            ->join('s.sortieEtat', 'e')
            ->where('e.libelle = :etat')
            ->setParameter('etat', 'Ouverte')
            ->getQuery()
            ->getResult();
    }
    public function findByEtatsAndUser(int $idParticipant)
    {
        return $this->createQueryBuilder('s')
            ->innerJoin('s.sortieEtat', 'e')
            ->innerJoin('s.sortieParticipant', 'p')
            ->andWhere('e.libelle IN (\'Ouverte\',\'Clôturée\',\'En cours\',\'Terminée\')')
            ->orWhere('e.libelle = \'En création\' AND s.sortieParticipant = :idParticipant')
            ->setParameter('idParticipant', $idParticipant)
            ->getQuery()
            ->getResult();
    }

    public function findBySortiesOuvertes(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id')
            ->innerJoin('s.sortieEtat', 'e')
            ->andWhere('e.libelle = :ouverte')
            ->andWhere('s.dateLimiteInscription < :dateHeure')
            ->setParameter('ouverte','Ouverte')
            ->setParameter('dateHeure',new \DateTimeImmutable())
            ->getQuery()
            ->getResult()
        ;
    }
    public function findBySortiesCloturees(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id')
            ->innerJoin('s.sortieEtat', 'e')
            ->andWhere('e.libelle = :cloturee')
            ->andWhere('s.dateHeureDebut < :dateHeure')
            ->setParameter('cloturee','Clôturée')
            ->setParameter('dateHeure',new \DateTimeImmutable())
            ->getQuery()
            ->getResult()
        ;
    }
    public function findBySortiesEncours(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id')
            ->innerJoin('s.sortieEtat', 'e')
            ->andWhere('e.libelle = :encours')
            ->andWhere('ADDTIME(s.dateHeureDebut, s.duree) < NOW()')
            ->setParameter('encours','En cours')
            ->getQuery()
            ->getResult();
        
    }
    public function findBySortiesTermineesAnnulees(): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.id')
            ->innerJoin('s.sortieEtat', 'e')
            ->andWhere('e.libelle = :terminee')
            ->orWhere('e.libelle = :annulee')
            ->andWhere('DATE_ADD(s.dateHeureDebut, :mois,\'MONTH\') < :dateHeure')
            ->setParameter('terminee','Terminée')
            ->setParameter('annulee','Annulée')
            ->setParameter('mois',1)
            ->setParameter('dateHeure',new \DateTimeImmutable())
            ->getQuery()
            ->getResult()
        ;
    }
    public function updateEtat(int $etatId, array $sortiesIds): void
    {
        $this->createQueryBuilder('s')
            ->update()
            ->set('s.sortieEtat', ':etatId')
            ->andWhere('s.id IN (:sortiesIds)')
            ->setParameter('etatId',$etatId)
            ->setParameter('sortiesIds',$sortiesIds)
            ->getQuery()
            ->execute()
        ;
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
