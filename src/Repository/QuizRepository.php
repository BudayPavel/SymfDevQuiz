<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;

class QuizRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function countFiltered($filter)
    {
        $dql = 'SELECT COUNT(quiz) FROM App\Entity\Quiz quiz ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (quiz.'.$search.' LIKE :search) OR';
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
        $dql = 'SELECT quiz FROM App\Entity\Quiz quiz ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (quiz.'.$search.' LIKE :search) OR';
            }
            $dql = substr($dql, 0, strlen($dql) - 3);
        }

        if ($filter['orderField']!="") {
            $dql .= ' ORDER BY quiz.'.$filter['orderField'].' '.$filter['order'];
        }

        $query = $this->getEntityManager()->createQuery($dql)
            ->setMaxResults($filter['records_per_page'])
            ->setFirstResult($filter['start']);

        if ($filter['searchFields']!=null) {
            $query->setParameter('search', $filter['search'].'%');
        }

        return $query->execute();
    }

    public function findQuestions($id):array
    {
        return $this->createQueryBuilder('quiz')
            // p.category refers to the "category" property on product
            ->innerJoin('quiz.questions', 'quest')
            // selects all the category data to avoid the query
            ->andWhere('quiz.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}