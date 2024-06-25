<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */


class ProductRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns a product
     */

    public function findOne($id): ?Product
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.id = :val')
            ->setParameter($id, 'val')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Product[] Returns an array of the list of product from a catalog
     */

    public function findByCategory($category)
    {
        return $this->createQueryBuilder('product')
            ->andWhere('product.category = :val')
            ->setParameter('val', $category)
            ->orderBy('product.id', 'ASC')
            ->setMaxResults(10) // pas nécéssairement
            ->getQuery()
            ->getResult();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('product')
            ->getQuery()
            ->getResult();
    }
}
