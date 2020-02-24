<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\TraductionSource;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TraductionSourceVoter extends Voter
{
	public const EDIT = 'edit';
	public const DELETE = 'delete';


	private const ATTRIBUTES = [
		self::EDIT,
		self::DELETE,
	];

	protected function supports($attribute, $subject)
	{
		return $subject instanceof TraductionSource
			&& in_array($attribute, self::ATTRIBUTES);
	}

	/**
	 * @param string $a
	 * @param TraductionSource $p
	 * @param TokenInterface $t
	 *
	 * @return bool
	 */
	protected function voteOnAttribute(
		$attribute,
		$traductionSource,
		TokenInterface $token
	) {
		switch ($attribute) {
			case self::EDIT:
				return $this->isOwner($token->getUser(), $traductionSource);
			case self::DELETE:
				return $this->isOwner($token->getUser(), $traductionSource);
		}

		throw new \LogicException('Invalid attribute: ' . $attribute);
	}

	private function isOwner(?User $user, TraductionSource $traductionSource)
	{
		if (!$user) {
			return false;
		}

		if (in_array('ROLE_ADMIN', $user->getRoles())) {
			return self::ACCESS_GRANTED;
		}

		return $user->getId() === $traductionSource->getProjectId()->getUser()->getId();
	}
}
