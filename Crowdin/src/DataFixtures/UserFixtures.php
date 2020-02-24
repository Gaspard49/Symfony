<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\TraductionSource;
use App\Entity\TraductionTarget;
use App\Entity\Language;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}

	public function load(ObjectManager $manager)
	{
		// On configure dans quelles langues nous voulons nos données
		$faker = Faker\Factory::create('en_EN');
		$language = new Language();
		$language->setLanguageName('French');
		$manager->persist($language);

		$language = new Language();
		$language->setLanguageName('English');
		$manager->persist($language);

		$language = new Language();
		$language->setLanguageName('German');
		$manager->persist($language);


		//creation admin
		$user = new User();
		$user->setPseudo('ADMIN');
		$user->addLanguage($language);
		$user->setDescription($faker->paragraph);
		$user->setRoles(array('ROLE_ADMIN'));
		$user->setEmail(sprintf('admin@admin.com'));
		$user->setPassword($this->passwordEncoder->encodePassword(
			$user,
			'admin'
		));

		$manager->persist($user);

		// on créé 10 users
		for ($i = 0; $i < 10; $i++) {
			$user = new User();
			$user->setPseudo($faker->firstName);
			$user->addLanguage($language);
			$user->setDescription($faker->paragraph);
			$user->setRoles(array('ROLE_USER'));
			$user->setEmail(sprintf('user%d@yopmail.com', $i));
			$user->setPassword($this->passwordEncoder->encodePassword(
				$user,
				'password'
			));
			for ($l = 0; $l < rand(1,5); $l++) {
				$project = new Project();
				$project->setName($faker->company);
				$project->setUrl($faker->url);
				$project->setUser($user);
				$project->setLanguageId($language);
				for ($j = 0; $j < rand(1,5); $j++) {
					$traductionSource = new TraductionSource();
					$traductionSource->setProject($project);
					$traductionSource->setSource($faker->realText($maxNbChars = 15, $indexSize = 1));
					for ($k = 0; $k < rand(0,1); $k++) {
						$traductionTarget = new TraductionTarget();
						$traductionTarget->setTraductionSourceId($traductionSource);
						$traductionTarget->setUserId($user);
						$traductionTarget->setLanguageId($language);
						$traductionTarget->setTarget($faker->realText($maxNbChars = 15, $indexSize = 1));
						$manager->persist($traductionTarget);
					}


					$manager->persist($traductionSource);
				}
				$manager->persist($project);
			}
			$manager->persist($user);
		}
	
		$manager->flush();
	}
}
