<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
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

    public function findAllFiltered($filter): array
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT p
        FROM App\Entity\User p
        WHERE (p.email LIKE :search)
        ORDER BY :sortby DESC'
        )->setParameters(['search' =>  $filter['search'].'%', 'sortby' => 'p.id'])
        ->setMaxResults($filter['records_per_page'])
        ->setFirstResult($filter['start']);

        return $query->execute();
    }

    public function countFiltered($filter): int
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT COUNT(p)
        FROM App\Entity\User p
        WHERE (p.email LIKE :search)
        ORDER BY p.id ASC'
        )->setParameter('search', $filter['search'].'%');

        return (int)$query->execute()[0]['1'];
    }

    public function findAllFiltered1($filter): array
    {
        $dql = 'SELECT user FROM App\Entity\User user ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (user.'.$search.' LIKE :search) OR';
            }
            $dql = substr($dql, 0, strlen($dql) - 3);
        }

        if ($filter['orderField']!="") {
            $dql .= ' ORDER BY user.'.$filter['orderField'].' '.$filter['order'];
        }

        $query = $this->getEntityManager()->createQuery($dql)
        ->setMaxResults($filter['records_per_page'])
        ->setFirstResult($filter['start']);

        if ($filter['searchFields']!=null) {
            $query->setParameter('search', $filter['search'].'%');
        }


//        var_dump($query->getSQL());
//        var_dump($query->getParameter('search'));
//        die;

        return $query->execute();
    }
}
