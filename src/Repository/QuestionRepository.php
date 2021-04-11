<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function countFiltered($filter)
    {
        $dql = 'SELECT COUNT(question) FROM App\Entity\Question question ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (question.'.$search.' LIKE :search) OR';
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
        $dql = 'SELECT question FROM App\Entity\Question question ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (question.'.$search.' LIKE :search) OR';
            }
            $dql = substr($dql, 0, strlen($dql) - 3);
        }

        if (isset($filter['orderField']) && $filter['orderField']!="") {
            $dql .= ' ORDER BY question.'.$filter['orderField'].' '.$filter['order'];
        }

        $query = $this->getEntityManager()->createQuery($dql)
            ->setMaxResults($filter['records_per_page'])
            ->setFirstResult($filter['start']);

        if ($filter['searchFields']!=null) {
            $query->setParameter('search', $filter['search'].'%');
        }

        return $query->execute();
    }

    public function findJoinedAnswers($questionId)
    {
        return $this->createQueryBuilder('q')
            // p.category refers to the "category" property on product
            ->innerJoin('q.answers', 'a')
            // selects all the category data to avoid the query
            ->andWhere('a.question = :id')
            ->setParameter('id', $questionId)
            ->getQuery()
            ->getResult();
    }
}
