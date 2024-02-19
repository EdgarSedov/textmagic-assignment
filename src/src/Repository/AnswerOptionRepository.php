<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AnswerOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnswerOption>
 *
 * @method AnswerOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnswerOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnswerOption[]    findAll()
 * @method AnswerOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnswerOption::class);
    }
}
