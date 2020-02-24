<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Project;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
	public const CREATE = 'create';
	public const EDIT = 'edit';
	public const DELETE = 'delete';


	private const ATTRIBUTES = [
		self::CREATE,
		self::EDIT,
		self::DELETE,
	];

	protected function supports($attribute, $subject)
	{
		return $subject instanceof Project
			&& in_array($attribute, self::ATTRIBUTES);
	}

	/**
	 * @param string $a
	 * @param Project $p
	 * @param TokenInterface $t
	 *
	 * @return bool
	 */
	protected function voteOnAttribute(
		$attribute,
		$project,
		TokenInterface $token
	) {
		switch ($attribute) {
			case self::CREATE:
				return $this->isOwner($token->getUser(), $project);
			case self::EDIT:
				return $this->isOwner($token->getUser(), $project);
			case self::DELETE:
				return $this->isOwner($token->getUser(), $project);
		}

		throw new \LogicException('Invalid attribute: ' . $attribute);
	}

	private function isOwner(?User $user, Project $project)
	{
		if (!$user) {
			return false;
		}
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return self::ACCESS_GRANTED;
            }
		return $user->getId() === $project->getUser()->getId();
	}
}
