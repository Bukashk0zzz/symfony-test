<?php declare(strict_types = 1);

namespace App\Repository\User;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getAllQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->orderBy('c.id', 'DESC')
        ;
    }
}
