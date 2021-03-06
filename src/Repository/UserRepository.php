<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function countFiltered($filter)
    {
        $dql = 'SELECT COUNT(user) FROM App\Entity\User user ';
        if ($filter['searchFields']!=null) {
            $dql .= 'WHERE';
            foreach ($filter['searchFields'] as $search) {
                $dql .= ' (user.'.$search.' LIKE :search) OR';
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

        return $query->execute();
    }

    public function findQuizRes($params)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT q.id, q.name AS Name, Ali2.Correct, Ali3.Total, sum(r.time) AS Time FROM result r
                JOIN quiz q ON q.id = r.quiz_id
                JOIN (SELECT count(a.id) as useranswers, q.id as qid FROM result r
                    JOIN answer a ON r.answer_id = a.id
                    JOIN quiz q ON q.id = r.quiz_id
                    WHERE (r.user_id = :uid) 
                    GROUP BY qid) AS Ali1 ON Ali1.qid=q.id
                JOIN (SELECT count(a.id) as Correct, q.id as qid FROM result r
                    JOIN answer a ON r.answer_id = a.id
                    JOIN quiz q ON q.id = r.quiz_id
                    WHERE (a.correct = TRUE) AND (r.user_id = :uid)
                    GROUP BY q.id) AS Ali2 ON Ali2.qid=q.id
                JOIN (SELECT count(qq2.question_id) as Total, qz.id as qid FROM quiz qz
                    JOIN quiz_questions qq2 ON qz.id=qq2.quiz_id
                    JOIN question que ON que.id=qq2.question_id
                    GROUP BY qz.id) AS Ali3 ON Ali3.qid=q.id
                WHERE (r.user_id = :uid) AND (Ali1.useranswers = Ali3.Total)   
                GROUP BY q.id, q.name, Ali2.Correct, Ali3.Total
                LIMIT '.$params['records_per_page'].'
                OFFSET '.$params['start'].'
                ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $params['user']]);
        return $stmt->fetchAll();
    }

    public function countQuizRes($params)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT q.name AS Name, Ali2.Correct, Ali3.Total, sum(r.time) AS Time FROM result r
                JOIN quiz q ON q.id = r.quiz_id
                JOIN (SELECT count(a.id) as useranswers, q.id as qid FROM result r
                    JOIN answer a ON r.answer_id = a.id
                    JOIN quiz q ON q.id = r.quiz_id
                    WHERE (r.user_id = :uid) 
                    GROUP BY qid) AS Ali1 ON Ali1.qid=q.id
                JOIN (SELECT count(a.id) as Correct, q.id as qid FROM result r
                    JOIN answer a ON r.answer_id = a.id
                    JOIN quiz q ON q.id = r.quiz_id
                    WHERE (a.correct = TRUE) AND (r.user_id = :uid)
                    GROUP BY q.id) AS Ali2 ON Ali2.qid=q.id
                JOIN (SELECT count(qq2.question_id) as Total, qz.id as qid FROM quiz qz
                    JOIN quiz_questions qq2 ON qz.id=qq2.quiz_id
                    JOIN question que ON que.id=qq2.question_id
                    GROUP BY qz.id) AS Ali3 ON Ali3.qid=q.id
                WHERE (r.user_id = :uid) AND (Ali1.useranswers = Ali3.Total)   
                GROUP BY q.name, Ali2.Correct, Ali3.Total
                ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $params['user']]);
        return count($stmt->fetchAll());
    }
}
