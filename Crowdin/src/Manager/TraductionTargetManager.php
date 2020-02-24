<?php

namespace App\Manager;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Repository\TraductionTargetRepository;
use App\Entity\TraductionTarget;


class TraductionTargetManager
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


    public function add(TraductionTarget $traductionTarget): void
    {
        $this->entityManager->persist($traductionTarget);
    }

    public function delete(TraductionTarget $traductionTarget): void
    {
        $this->entityManager->remove($traductionTarget);
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
