<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\TraductionTarget;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TraductionTargetVoter extends Voter
{
	public const EDIT = 'edit';
	public const DELETE = 'delete';


	private const ATTRIBUTES = [
		self::EDIT,
		self::DELETE,
	];

	protected function supports($attribute, $subject)
	{
		return $subject instanceof TraductionTarget
			&& in_array($attribute, self::ATTRIBUTES);
	}

	/**
	 * @param string $a
	 * @param TraductionTarget $p
	 * @param TokenInterface $t
	 *
	 * @return bool
	 */
	protected function voteOnAttribute(
		$attribute,
		$traductionTarget,
		TokenInterface $token
	) {
		switch ($attribute) {
			case self::EDIT:
				return $this->isOwner($token->getUser(), $traductionTarget) ;
			case self::DELETE:
				return $this->isOwner($token->getUser(), $traductionTarget);
		}

		throw new \LogicException('Invalid attribute: ' . $attribute);
	}

	private function isOwner(?User $user, TraductionTarget $traductionTarget)
	{
		if (!$user) {
			return false;
		}
		
		if (in_array('ROLE_ADMIN', $user->getRoles())) {
			return self::ACCESS_GRANTED;
		}

		return $user->getId() === $traductionTarget->getUser()->getId() ? true : $user->getId() === $traductionTarget->getTraductionSourceId()->getProjectId()->getUser()->getId();
	}
}
