<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $filter
     * @return User[]
     */
    public function findAllByPattern(array $filter, array $sort): array
    {
        // automatically knows to select Products
        // the "p" is an alias you'll use in the rest of the query
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.'.$filter['filter']." LIKE '".$filter['pattern']."%'")
            //->setParameter('value', $filter['pattern'])
            ->orderBy('p.'.$sort['sort'], $sort['order'])
            ->getQuery();

        return $qb->execute();
    }


}
