<?php

namespace App\Manager;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Repository\TraductionSourceRepository;
use App\Entity\TraductionSource;


class TraductionSourceManager
{
	 /** @var EntityManagerInterface  */
	 private $entityManager;

	 /** @var EntityRepository */
	 protected $repository;

	 public function __construct(EntityManagerInterface $entityManager)
	 {
		 $this->setEntityManager($entityManager);
	 }


    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */


    public function add(TraductionSource $traductionSource): void
    {
        $this->entityManager->persist($traductionSource);
    }

    public function delete(TraductionSource $traductionSource): void
    {
        $this->entityManager->remove($traductionSource);
    }

    public function save()
    {
        $this->entityManager->flush();
    }

    public function findBy(array $conditions, array $order)
    {
        $this->getRepository()->findBy($conditions, $order);
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
