<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function getSingle(string $sid): ?array
    {
        $user = $this->findOneBy(['sid' => $sid, 'deleted' => null]);
        if(false === $user instanceof User){
            return null;
        }

        return $this->toArray($user);
    }

    public function getAll(int $offset, int $limit): ?array
    {
        $users = $this->findBy([], [], $limit, $offset);

        return array_map(fn($user) => $this->toArray($user), $users);
    }

    public function update(User $user, array $data): ?\Throwable
    {


        $em = $this->getEntityManager();
        $em->beginTransaction();
        try{
            if(true === isset($data['email'])){
                $exist = $this->findOneBy(['email' => $data['email'], 'deleted' => null]);

                if(false === $exist instanceof User){
                    $user->setEmail($data['email']);
                }
            }

            if(true === isset($data['password'])){
                $hasherPassword = $this->userPasswordHasher->hashPassword($user, $data['password']);
                $user->setPassword($hasherPassword);
            }

            if(true === isset($data['roles'])){
                $user->setRoles($data['roles']);
            }

            $em->persist($user);
            $em->flush();
            $em->commit();
        }catch (\Throwable $throwable){
            $em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable;
        }

        return null;

    }

    public function delete(string $sid): ?\Throwable
    {
        $user = $this->findOneBy(['sid' => $sid, 'deleted' => null]);
        if (false === $user instanceof User) {
            return new \Exception('User not found');
        }

        $em = $this->getEntityManager();
        $em->beginTransaction();
        try{
            $user->setDeleted(new \DateTime('now'));
            $em->persist($user);
            $em->flush();
            $em->commit();
        }catch (\Throwable $throwable){
            $em->rollback();
            $this->logger->error($throwable->getMessage(), ['exception' => $throwable]);
            return $throwable;
        }

        return null;
    }

    private function toArray(User $user):array
    {
        return [
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'created' => $user->getCreated()->format('Y-m-d H:i:s'),
            'updated' => $user->getUpdated()->format('Y-m-d H:i:s'),
        ];
    }
}
