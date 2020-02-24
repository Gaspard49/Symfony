<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Manager\ProjectManager;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
	/**
	 * @Route("/", name="project_index", methods={"GET"})
	 */
	public function index(ProjectRepository $repository, Request $request, PaginatorInterface $paginator): Response
	{
		$data =  $repository->findAll();
		$projects= $paginator->paginate(
		$data, // Requête contenant les données à paginer (ici nos articles)
			$request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
			20 // Nombre de résultats par page
		);



		return $this->render('project/index.html.twig', [
			'projects' => $projects,
		]);
	}

	/**
	 * @Route("/new", name="project_new", methods={"GET","POST"})
	 */
	public function new(Request $request, ProjectManager $manager): Response
	{
		$project = new Project();
		//Get user id
		$project->setUser($this->getUser());
		$form = $this->createForm(ProjectType::class, $project);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$manager->add($project);
			$manager->save();

			return $this->redirectToRoute('project_index');
		}

		return $this->render('project/new.html.twig', [
			'project' => $project,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="project_show", methods={"GET"})
	 */
	public function show(Project $project): Response
	{
		return $this->render('project/show.html.twig', [
			'project' => $project,
		]);
	}

	/**
	 * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, Project $project, ProjectManager $manager): Response
	{
		$this->denyAccessUnlessGranted('edit', $project);
		$form = $this->createForm(ProjectType::class, $project);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$file = $form['file']->getData();

			// this condition is needed because the 'brochure' field is not required
			// so the PDF file must be processed only when a file is uploaded
			if ($file) {
				$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
				// this is needed to safely include the file name as part of the URL
				$newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();

				// Move the file to the directory where brochures are stored
				try {
					$file->move(
						$this->getParameter('files_directory'),
						$newFilename
					);
				} catch (FileException $e) {
					// ... handle exception if something happens during file upload
				}

				// updates the 'brochureFilename' property to store the PDF file name
				// instead of its contents
				$project->setFilename($newFilename);
			}
			$manager->save();

			return $this->redirectToRoute('project_index');
		}

		return $this->render('project/edit.html.twig', [
			'project' => $project,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="project_delete", methods={"DELETE"})
	 */
	public function delete(Request $request, Project $project, ProjectManager $manager): Response
	{
		$this->denyAccessUnlessGranted('delete', $project);
		if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
			$manager->delete($project);
			$manager->save();
		}

		return $this->redirectToRoute('project_index');
	}
}
