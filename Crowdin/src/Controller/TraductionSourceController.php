<?php

namespace App\Controller;

use App\Entity\TraductionSource;
use App\Entity\Project;
use App\Manager\TraductionSourceManager;
use App\Form\TraductionSourceType;
use App\Form\TraductionSourceCSVType;
use App\Repository\TraductionSourceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use League\Csv\Reader;
use Knp\Component\Pager\PaginatorInterface;



/**
 * @Route("/traduction/source")
 */
class TraductionSourceController extends AbstractController
{
	/**
	 * @Route("/", name="traduction_source_index", methods={"GET"})
	 */
	public function index(TraductionSourceRepository $repository, Request $request, PaginatorInterface $paginator): Response
	{

		$data =  $repository->findAll();
		$traductionSources = $paginator->paginate(
			$data, // Requête contenant les données à paginer (ici nos articles)
			$request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
			20 // Nombre de résultats par page
		);

		return $this->render('traduction_source/index.html.twig', [
			'traduction_sources' => $traductionSources
		]);
	}

	/**
	 * @Route("/new/{id}", name="traduction_source_new", methods={"GET","POST"})
	 * 
	 */
	public function new(Project $project, Request $request, TraductionSourceManager $manager): Response
	{
		$this->denyAccessUnlessGranted('create', $project);
		$traductionSource = new TraductionSource();
		$traductionSource->setProjectId($project);
		$form = $this->createForm(TraductionSourceType::class, $traductionSource);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$manager->add($traductionSource);
			$manager->save();

			return $this->redirectToRoute('traduction_source_index');
		}

		return $this->render('traduction_source/new.html.twig', [
			'traduction_source' => $traductionSource,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/newCSV/{id}", name="traduction_source_CSV_new", methods={"GET","POST"})
	 * 
	 */
	public function newCSV(Project $project, Request $request, TraductionSourceManager $manager): Response
	{
		$this->denyAccessUnlessGranted('create', $project);
		$traductionSource = new TraductionSource();
		$form = $this->createForm(TraductionSourceCSVType::class, $traductionSource);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$file = $form['file']->getData();

			if ($file) {
				$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
				// this is needed to safely include the file name as part of the URL
				$newFilename = $originalFilename . '-' . uniqid() . '.' . 'csv';

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
			$results = Reader::createFromPath($this->getParameter('files_directory') . $project->getFileName());
			$results->setHeaderOffset(0);

			foreach ($results as $row) {

				// create new athlete
				$traductionSource = new TraductionSource();
				$traductionSource->setProjectId($project);
				$traductionSource->setSource($row['source']);

				$manager->add($traductionSource);
			}

			$manager->save();

			return $this->redirectToRoute('traduction_source_index');
		}

		return $this->render('traduction_source/newCSV.html.twig', [
			'traduction_source' => $traductionSource,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="traduction_source_show", methods={"GET"})
	 */
	public function show(TraductionSource $traductionSource): Response
	{
		return $this->render('traduction_source/show.html.twig', [
			'traduction_source' => $traductionSource,
		]);
	}
	

	/**
	 * @Route("/{id}/edit", name="traduction_source_edit", methods={"GET","POST"})
	 */
	public function edit(Request $request, TraductionSource $traductionSource, TraductionSourceManager $manager): Response
	{
		$this->denyAccessUnlessGranted('edit', $traductionSource);
		$form = $this->createForm(TraductionSourceType::class, $traductionSource);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$manager->save();

			return $this->redirectToRoute('traduction_source_index');
		}

		return $this->render('traduction_source/edit.html.twig', [
			'traduction_source' => $traductionSource,
			'form' => $form->createView(),
		]);
	}

	/**
	 * @Route("/{id}", name="traduction_source_delete", methods={"DELETE"})
	 */
	public function delete(Request $request, TraductionSource $traductionSource, TraductionSourceManager $manager): Response
	{
		$this->denyAccessUnlessGranted('delete', $traductionSource);
		if ($this->isCsrfTokenValid('delete' . $traductionSource->getId(), $request->request->get('_token'))) {
			$manager->delete($traductionSource);
			$manager->save();
		}

		return $this->redirectToRoute('traduction_source_index');
	}
}

/*
 	$reader = Reader::createFromPath('%kernel.root_dir%/../public/uploads/files/%$newFilename%');
			$results = $reader->fetchAssoc();

			foreach ($results as $row) {

				// create new athlete
				$traductionSource = new TraductionSource();
				$traductionSource->setProjectId($project);
				$traductionSource->setSource($row['source']);
	
				$traductionSourceManager->add($traductionSource);
				
			}
	
			$traductionSourceManager->save();
			*/
