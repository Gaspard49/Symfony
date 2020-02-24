<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegistrationFormType;
use App\Security\AuthentificatorAuthenticator;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;


class SecurityController extends AbstractController
{
	/**
	 * @Route("/login", name="app_login")
	 */
	public function login(AuthenticationUtils $authenticationUtils): Response
	{


		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
	}

	/**
	 * @Route("/register", name="app_register")
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AuthentificatorAuthenticator $authenticator, UserManager $manager): Response
	{
		$user = new User();
		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// encode the plain password
			$user->setPassword(
				$passwordEncoder->encodePassword(
					$user,
					$form->get('plainPassword')->getData()
				)
			);
			$role = $user->getRoles(); 
			$user->setRoles($role);
		 
			$manager->add($user);
			$manager->save();

			// do anything else you need here, like send an email

			return $guardHandler->authenticateUserAndHandleSuccess(
				$user,
				$request,
				$authenticator,
				'main' // firewall name in security.yaml
			);
		}

		return $this->render('registration/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}

	/**
	 * @Route("/forgotten_password", name="app_forgotten_password")
	 */
	public function forgottenPassword(Request $request, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator, UserManager $manager): Response
	{
		if ($request->isMethod('POST')) {
			$email = $request->request->get('email');
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
			/* @var $user User */
			if ($user === null) {
				$this->addFlash('danger', 'Email Inconnu');
				return $this->redirectToRoute('app_forgotten_password');
			}
			$token = $tokenGenerator->generateToken();
			try {
				$user->setResetToken($token);
				$manager->save();
			} catch (\Exception $e) {
				$this->addFlash('warning', $e->getMessage());
				return $this->redirectToRoute('app_forgotten_password');
			}
			$url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
			$message = (new \Swift_Message('Forgot Password'))
				->setFrom('g.ponty@dev-web.io')
				->setTo($user->getEmail())
				->setBody(
					"blablabla voici le token pour reseter votre mot de passe : " . $url,
					'text/html'
				);
			$mailer->send($message);
			$this->addFlash('notice', 'Mail envoyé');
			return $this->redirectToRoute('app_forgotten_password');
		}
		return $this->render('security/forgotten_password.html.twig');
	}


	/**
	 * @Route("/reset_password/{token}", name="app_reset_password")
	 */
	public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder, UserManager $manager)
	{
		if ($request->isMethod('POST')) {
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository(User::class)->findOneBy(['resetToken' => $token]);

			/* @var $user User */
			if ($user === null) {
				$this->addFlash('danger', 'Token Inconnu');
				return $this->redirectToRoute('app_forgotten_password');
			}
			$user->setResetToken(null);
			$user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
			$manager->save();
			$this->addFlash('notice', 'Mot de passe mis à jour');
			return $this->redirectToRoute('app_forgotten_password');
		} else {
			return $this->render('security/reset_password.html.twig', ['token' => $token]);
		}
	}


	/**
	 * @Route("/logout", name="app_logout")
	 */
	public function logout()
	{
		throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
	}

	
}
