<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function findJoinedAnswers($questionId)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.question = :id')
            ->setParameter('id', $questionId)
            ->getQuery()
            ->getResult();
    }

    public function countFiltered($filter)
    {
        $dql = 'SELECT COUNT(answer) FROM App\Entity\Answer answer ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (answer.'.$search.' LIKE :search) OR';
            }
            $dql = substr($dql, 0, strlen($dql) - 3);
        }

        $query = $this->getEntityManager()->createQuery($dql);

        if ($filter['searchFields']!=null) {
            $query->setParameter('search', $filter['search'].'%');
        }

        return $query->execute()[0][1];
    }

    public function findAllFiltered($filter): array
    {
        $dql = 'SELECT answer FROM App\Entity\Answer answer ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (answer.'.$search.' LIKE :search) OR';
            }
            $dql = substr($dql, 0, strlen($dql) - 3);
        }

        if ($filter['orderField']!="") {
            $dql .= ' ORDER BY answer.'.$filter['orderField'].' '.$filter['order'];
        }

        $query = $this->getEntityManager()->createQuery($dql)
            ->setMaxResults($filter['records_per_page'])
            ->setFirstResult($filter['start']);

        if ($filter['searchFields']!=null) {
            $query->setParameter('search', $filter['search'].'%');
        }

        return $query->execute();
    }
}
