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

    public function findNotFinshed($params): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT myQuiz.id, myQuiz.name as Name, Ali1.qc as Answered, Ali2.total as Total, Ali1.sumtime as Time FROM quiz myQuiz
                JOIN (SELECT q.id as idd, q.name, count(r.question_id) AS qc,sum(r.time) as sumtime, r.user_id FROM (quiz q
                JOIN quiz_questions qq ON q.id = qq.quiz_id
                JOIN question qu ON qq.question_id=qu.id
                JOIN result r ON (q.id = r.quiz_id) AND (qu.id = r.question_id))
                WHERE (r.user_id = :uid)
                GROUP BY q.id, q.name, r.user_id) AS Ali1 ON Ali1.idd=myQuiz.id
                JOIN (SELECT count(qq2.question_id) as total, qz.id as quizid FROM quiz qz
                                            JOIN quiz_questions qq2 ON qz.id=qq2.quiz_id
                                            JOIN question que ON que.id=qq2.question_id
                                            GROUP BY qz.id) AS Ali2 ON Ali2.quizid=myQuiz.id
                WHERE Ali1.qc < Ali2.total
                LIMIT '.$params['records_per_page'].'
                OFFSET '.$params['start'];
        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $params['user'],]);
        return $stmt->fetchAll();
    }

    public function countNotFinshed($params)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT myQuiz.id, myQuiz.name as Name, Ali1.Answered, Ali2.Total, Ali1.sumtime as Time FROM quiz myQuiz
                JOIN (SELECT q.id as idd, q.name, count(r.question_id) AS Answered,sum(r.time) as sumtime, r.user_id FROM (quiz q
                JOIN quiz_questions qq ON q.id = qq.quiz_id
                JOIN question qu ON qq.question_id=qu.id
                JOIN result r ON (q.id = r.quiz_id) AND (qu.id = r.question_id))
                WHERE (r.user_id = :uid)
                GROUP BY q.id, q.name, r.user_id) AS Ali1 ON Ali1.idd=myQuiz.id
                JOIN (SELECT count(qq2.question_id) as total, qz.id as quizid FROM quiz qz
                                            JOIN quiz_questions qq2 ON qz.id=qq2.quiz_id
                                            JOIN question que ON que.id=qq2.question_id
                                            GROUP BY qz.id) AS Ali2 ON Ali2.quizid=myQuiz.id
                WHERE Ali1.Answered < Ali2.Total';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $params['user'],]);
        return count($stmt->fetchAll());
    }

    public function findNotStarted($params): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM quiz quz
                WHERE quz.id NOT IN (SELECT res.quiz_id FROM result res GROUP BY res.quiz_id)
                LIMIT '.$params['records_per_page'].'
                OFFSET '.$params['start'];

        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $params['user']]);
        return $stmt->fetchAll();
    }

    public function countNotStarted($params)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM quiz quz
                WHERE quz.id NOT IN (SELECT res.quiz_id FROM result res GROUP BY res.quiz_id)';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['uid' => $params['user']]);
        return count($stmt->fetchAll());
    }
}