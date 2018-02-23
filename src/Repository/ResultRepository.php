<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Result;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ResultRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Result::class);
    }

    public function findQuizTop(Quiz $quiz)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT count(a.id), u.first_name, u.last_name FROM result r
                JOIN answer a ON r.answer_id = a.id
                JOIN user u ON r.user_id = u.id
                WHERE (a.correct = TRUE) AND (r.quiz_id = :qid)
                GROUP BY u.first_name, u.last_name
                LIMIT 10';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['qid' => $quiz->getId()]);
        return $stmt->fetchAll();
    }
}