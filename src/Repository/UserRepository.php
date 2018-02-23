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

//    public function findQuizRes(User $user)
//    {
//        $conn = $this->getEntityManager()->getConnection();
//        $sql = 'SELECT * FROM result r
//                JOIN answer a ON r.answer_id = a.id
//                JOIN user u ON r.user_id = u.id
//                WHERE (a.correct = TRUE) AND (r.quiz_id = :qid)
//                GROUP BY u.first_name, u.last_name, r.time
//                ORDER BY c DESC, s DESC
//                LIMIT 10';
//        $stmt = $conn->prepare($sql);
//        $stmt->execute(['qid' => $quiz->getId()]);
//        return $stmt->fetchAll();
//    }
}
