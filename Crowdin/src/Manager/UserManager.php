<?php

namespace App\Manager;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


class UserManager extends ServiceEntityRepository  implements PasswordUpgraderInterface
{
	 /** @var EntityManagerInterface  */
	 private $entityManager;

	 /** @var EntityRepository */
	 protected $repository;

	 public function __construct(EntityManagerInterface $entityManager)
	 {
		 $this->setEntityManager($entityManager);
	 }

    public function add(User $user): void
    {
        $this->entityManager->persist($user);
    }

    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
    }

    public function save()
    {
        $this->entityManager->flush();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     *  -------------------
     *      ACCESSORS
     *  -------------------
     */

    public function getEntityManager(): EntityManagerInterface 
    {
        return $this->entityManager;
    }

    public function setEntityManager(EntityManagerInterface  $entityManager): self
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    public function getRepository(): EntityRepository
    {
        return $this->repository;
    }

    public function setRepository(EntityRepository $repository): self
    {
        $this->repository = $repository;
        return $this;
    }
}
