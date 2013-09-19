<?php

namespace Nzo\TunisiefretBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NotifMsgRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotifMsgRepository extends EntityRepository
{
    public function getNbMsgClient($id)
    {
         $qb = $this->createQueryBuilder('a');
         $qb->select('COUNT(a)')
            ->where('a.client = :client')
            ->andWhere('a.vu = 0')             
            ->setParameter('client', $id);
         return $qb->getQuery()->getSingleScalarResult();
    } 
    
    public function getListMessagesClient($id)
    {
         $qb = $this->createQueryBuilder('a');
         $qb->where('a.client = :client')
            ->orderBy('a.date', 'DESC')
            ->setParameter('client', $id);
         return $qb->getQuery();
    }
    
    public function getNbMsgExportateur($id)
    {
         $qb = $this->createQueryBuilder('a');
         $qb->select('COUNT(a)')
            ->where('a.exportateur = :exportateur')
            ->andWhere('a.vu = 0')             
            ->setParameter('exportateur', $id);
         return $qb->getQuery()->getSingleScalarResult();
    } 
    
    public function getListMessagesExportateur($id)
    {
         $qb = $this->createQueryBuilder('a');
         $qb->where('a.exportateur = :exportateur')
            ->orderBy('a.date', 'DESC')
            ->setParameter('exportateur', $id);
         return $qb->getQuery();
    }
}
