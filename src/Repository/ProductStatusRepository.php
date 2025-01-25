<?php

namespace App\Repository;

use App\Entity\ProductStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<ProductStatus>
 */
class ProductStatusRepository extends ServiceEntityRepository
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    )
    {
        parent::__construct($registry, ProductStatus::class);
    }

    public function add(array $data): ?\Throwable
    {
        $this->em->beginTransaction();

        try{
            $productStatus = (new ProductStatus())
                ->setName($data['name']);

            $this->em->persist($productStatus);
            $this->em->flush();
            $this->em->commit();
        }catch (\Throwable $throwable){
            $this->em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable;
        }

        return null;
    }

    public function getSingle(string $sid): ?array
    {
        $productStatus = $this->findOneBy(['sid' => $sid]);
        if(false === $productStatus instanceof ProductStatus){
            return null;
        }

        return $this->toArray($productStatus);
    }

    public function getAll(int $offset, int $limit): ?array
    {
        $productStatus = $this->findBy(['deleted' => null], [], $limit, $offset);

        return array_map(fn($ps) => $this->toArray($ps), $productStatus);
    }

    public function update(string $sid, array $data): ?\Throwable
    {
        $isExist = $this->findOneBy(['name' => $data['name'], 'deleted' => null]);
        if(true === $isExist instanceof ProductStatus){
            return new \Exception('ProductStatus name exist');
        }

        $productStatus = $this->findOneBy(['sid' => $sid]);

        $em = $this->getEntityManager();
        $em->beginTransaction();

        try{
            $productStatus->setName($data['name']);

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
        $productStatus = $this->findOneBy(['sid' => $sid, 'deleted' => null]);
        if (false === $productStatus instanceof ProductStatus) {
            return new \Exception('Product status not found');
        }

        $em = $this->getEntityManager();
        $em->beginTransaction();
        try{
            $productStatus->setDeleted(new \DateTime('now'));
            $em->persist($productStatus);
            $em->flush();
            $em->commit();
        }catch (\Throwable $throwable){
            $em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable;
        }

        return null;
    }

    private function toArray(ProductStatus $productStatus):array
    {
        return [
            'sid' => $productStatus->getSid(),
            'name' => $productStatus->getName(),
            'updated' => $productStatus->getUpdated()->format('Y-m-d H:i:s'),
        ];
    }

}
