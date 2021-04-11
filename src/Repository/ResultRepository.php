<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Result;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    public function findTop()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT count(a.id) as Answers, u.first_name as Firstname, u.last_name as Surname FROM result r
                JOIN answer a ON r.answer_id = a.id
                JOIN user u ON r.user_id = u.id
                WHERE (a.correct = TRUE)
                GROUP BY u.first_name, u.last_name
                ORDER BY Answers DESC
                LIMIT 5';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findQuizTop(Quiz $quiz)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT Answers, Total, count(a.id) as Points, u.first_name as Firstname, u.last_name as Surname, sum(r.time) as Time FROM result r
                JOIN answer a ON r.answer_id = a.id
                JOIN user u ON r.user_id = u.id
                JOIN (SELECT count(a.id) as Answers, q.id as qid, r.user_id as uid FROM result r
                    JOIN answer a ON r.answer_id = a.id
                    JOIN quiz q ON q.id = r.quiz_id
                    GROUP BY r.user_id, qid) AS Ali1 ON Ali1.qid=r.quiz_id AND Ali1.uid = r.user_id
                JOIN (SELECT count(qq2.question_id) as Total, qz.id as qid FROM quiz qz
                    JOIN quiz_questions qq2 ON qz.id=qq2.quiz_id
                    JOIN question que ON que.id=qq2.question_id
                    GROUP BY qz.id) AS Ali3 ON Ali3.qid=r.quiz_id
                WHERE (a.correct = TRUE) AND (r.quiz_id = :qid) AND (Answers=Total)
                GROUP BY Firstname, Surname, Answers, Total
                ORDER BY Points DESC, Time ASC
                LIMIT 10';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['qid' => $quiz->getId()]);
        return $stmt->fetchAll();
    }
}
