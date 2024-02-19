<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\QuestionnaireAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionnaireAttempt>
 *
 * @method QuestionnaireAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionnaireAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionnaireAttempt[]    findAll()
 * @method QuestionnaireAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionnaireAttemptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionnaireAttempt::class);
    }

    public function findWithDetails(int $attemptId): ?QuestionnaireAttempt
    {
        $qb = $this->createQueryBuilder('qa')
            ->leftJoin('qa.userAnswers', 'ua') // Join the userAnswers
            ->addSelect('ua')
            ->leftJoin('ua.question', 'q') // Join the questions of the user answers
            ->addSelect('q')
            ->leftJoin('q.answerOptions', 'ao') // Join the answer options of the questions
            ->addSelect('ao')
            ->where('qa.id = :id')
            ->setParameter('id', $attemptId)
            ->orderBy('ua.sequenceNumber', 'ASC'); // Order by sequenceNumber of the userAnswers

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }
}
