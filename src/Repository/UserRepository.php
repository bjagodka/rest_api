<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly LoggerInterface $logger,
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function add(array $data): ?string
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();
        try{
            $user = (new User())
                ->setEmail($data['email'])
                ->setRoles($data['role']);

            $hasherPassword = $this->userPasswordHasher->hashPassword($user, $data['password']);

            $user->setPassword($hasherPassword);
            $em->persist($user);
            $em->flush();
            $em->commit();
        }catch (\Throwable $throwable){
            $em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable->getMessage();
        }

        return null;
    }
}
