<?php

namespace App\Repository\Jira;

use App\Entity\Jira\Worklog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Worklog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Worklog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Worklog[]    findAll()
 * @method Worklog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorklogRepository extends ServiceEntityRepository
{
    
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Worklog::class);
    }
    
    /**
     * @param \DateTime|bool $startDate
     * @param \DateTime|bool $endDate
     * @return array
     */
    public function getTimeTable($startDate = false, $endDate = false)
    {
        if (!$startDate) {
            $startDate = new \DateTime('-7 days');
        }
        if (!$endDate) {
            $endDate = new \DateTime();
        }

        $query = $this->getEntityManager()->createQuery(
            'SELECT w.username, SUM(w.timeSpent) as timeSpent, date_format(w.created, \'YYYY-MM-DD\') as created 
                FROM App:Jira\Worklog w
                WHERE w.created >= :startDate AND w.created <= :endDate
                GROUP BY w.username, created
                ORDER BY w.username, created
            '
        )->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
        ;
        
        $query = $query->getResult();
        return $query;
    }

}
