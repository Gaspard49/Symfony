<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use App\Repository\UserRepository;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{


	/**
	 * @Route("/", name="user_index", methods={"GET"})
	 */
	public function index(Request $request, UserRepository $repository, PaginatorInterface $paginator): Response
	{
$data =  $repository->findAll();
		$users= $paginator->paginate(
		$data, // Requête contenant les données à paginer (ici nos articles)
			$request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
			20 // Nombre de résultats par page
		);

		return $this->render('user/index.html.twig', [
			'users' => $users,
		]);
	}

	/**
	 * @Route("/{id}", name="app_show")
	 */
	public function show(User $user)
	{
		$projects = $user->getProjects();

		$chart = new BarChart();
		$data = [['Project', 'Sources']];
		foreach ($projects as $project) {
				$data[] = array(
					$project->getName(), count($project->getTraductionSources()), 
				);
		}

		$chart->getData()->setArrayToDataTable(
			$data
		);

		$chart->getOptions()->getChart()
			->setTitle('Number of Source per project');
		$chart->getOptions()
			->setHeight(400)
			->setWidth(900)
			->setSeries([['axis' => 'distance'], ['axis' => 'brightness']])
			->setAxes([
				'x' => [
					'distance' => ['label' => 'parsecs'],
					'brightness' => ['side' => 'top', 'label' => 'apparent magnitude']
				]
			]);


		return $this->render('user/profile.html.twig', [
			'current' => $this->getUser(),
			'user' => $user,
			'chart' => $chart
		]);
	}


	/**
	 * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, User $user, UserManager $manager): Response
	{
		$this->denyAccessUnlessGranted('edit', $user);
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$manager->save();

			return $this->redirectToRoute('user_index');
		}

		return $this->render('user/edit.html.twig', [
			'user' => $user,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="user_delete", methods={"DELETE"})
	 */
	public function delete(Request $request, User $user, UserManager $manager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
			$manager->delete($user);
			$manager->save();
		}

		return $this->redirectToRoute('user_index');
	}
}
