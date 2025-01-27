<?php

namespace App\Repository;

use App\Entity\ProductCategory;
use App\Entity\ProductStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<ProductCategory>
 */
class ProductCategoryRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    )
    {
        parent::__construct($registry, ProductCategory::class);
    }

    public function getSingle(string $sid): ?array
    {
        $productCategory = $this->findOneBy(['sid' => $sid]);
        if(false === $productCategory instanceof ProductCategory){
            return null;
        }

        return $this->toArray($productCategory);
    }

    public function getAll(int $offset, int $limit): ?array
    {
        $productCategory = $this->findBy(['deleted' => null], [], $limit, $offset);

        return array_map(fn($ps) => $this->toArray($ps), $productCategory);
    }

    public function update(string $sid, array $data): ?\Throwable
    {
        $isExist = $this->findOneBy(['name' => $data['name'], 'deleted' => null]);
        if(true === $isExist instanceof ProductStatus){
            return new \Exception('ProductStatus name exist');
        }

        $productCategory = $this->findOneBy(['sid' => $sid]);

        $em = $this->getEntityManager();
        $em->beginTransaction();

        try{
            $productCategory->setName($data['name']);

            $em->flush();
            $em->commit();
        }catch (\Throwable $throwable){
            $this->em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable;
        }

        return null;
    }

    public function delete(string $sid): ?\Throwable
    {
        $productCategory = $this->findOneBy(['sid' => $sid, 'deleted' => null]);
        if (false === $productCategory instanceof ProductCategory) {
            return new \Exception('Product category not found');
        }

        $em = $this->getEntityManager();
        $em->beginTransaction();
        try{
            $productCategory->setDeleted(new \DateTime('now'));
            $em->persist($productCategory);
            $em->flush();
            $em->commit();
        }catch (\Throwable $throwable){
            $em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable;
        }

        return null;
    }

    public function add(array $data): ?\Throwable
    {
        $isExist = $this->findOneBy(['name' => $data['name'], 'deleted' => null]);
        if (true === $isExist instanceof ProductCategory) {
            return new \Exception('Name not found');
        }

        $this->em->beginTransaction();
        try{
            $productCategory = (new ProductCategory())
                ->setName($data['name']);

            $this->em->persist($productCategory);
            $this->em->flush();
            $this->em->commit();
        }catch (\Throwable $throwable){
            $this->em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable;
        }

        return null;
    }

    private function toArray(ProductCategory $productCategory):array
    {
        return [
            'sid' => $productCategory->getSid(),
            'name' => $productCategory->getName(),
            'updated' => $productCategory->getUpdated()->format('Y-m-d H:i:s'),
        ];
    }
}
