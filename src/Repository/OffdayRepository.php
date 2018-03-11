<?php

namespace App\Repository;

use App\Entity\Offday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Offday|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offday|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offday[]    findAll()
 * @method Offday[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OffdayRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Offday::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('o')
            ->where('o.something = :value')->setParameter('value', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
