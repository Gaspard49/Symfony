<?php

namespace App\Controller;

use App\Entity\TraductionTarget;
use App\Entity\TraductionSource;
use App\Manager\TraductionTargetManager;
use App\Form\TraductionTargetType;
use App\Repository\TraductionTargetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/traduction/target")
 */
class TraductionTargetController extends AbstractController
{
    /**
     * @Route("/", name="traduction_target_index", methods={"GET"})
     */
    public function index(TraductionTargetRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {

		$data =  $repository->findAll();
		$traductionTargets= $paginator->paginate(
		$data, // Requête contenant les données à paginer (ici nos articles)
			$request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
			20 // Nombre de résultats par page
		);


        return $this->render('traduction_target/index.html.twig', [
            'traduction_targets' =>$traductionTargets,
        ]);
    }

    /**
     * @Route("/new/{id}", name="traduction_target_new", methods={"GET","POST"})
     */
    public function new(TraductionSource $traductionSource, Request $request, TraductionTargetManager $manager): Response
    {
		$traductionTarget = new TraductionTarget();
		$traductionTarget->setUser($this->getUser());
		$traductionTarget->setTraductionSourceId($traductionSource);
        $form = $this->createForm(TraductionTargetType::class, $traductionTarget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
			$manager->add($traductionTarget);
            $manager->save();

            return $this->redirectToRoute('traduction_target_index');
        }

        return $this->render('traduction_target/new.html.twig', [
            'traduction_target' => $traductionTarget,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="traduction_target_show", methods={"GET"})
     */
    public function show(TraductionTarget $traductionTarget): Response
    {
        return $this->render('traduction_target/show.html.twig', [
            'traduction_target' => $traductionTarget,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="traduction_target_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TraductionTarget $traductionTarget, TraductionTargetManager $manager): Response
    {
		$this->denyAccessUnlessGranted('edit', $traductionTarget);
        $form = $this->createForm(TraductionTargetType::class, $traductionTarget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save();

            return $this->redirectToRoute('traduction_target_index');
        }

        return $this->render('traduction_target/edit.html.twig', [
            'traduction_target' => $traductionTarget,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="traduction_target_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TraductionTarget $traductionTarget, TraductionTargetManager $manager): Response
    {
		$this->denyAccessUnlessGranted('delete', $traductionTarget);
        if ($this->isCsrfTokenValid('delete'.$traductionTarget->getId(), $request->request->get('_token'))) {
			$manager->delete($traductionTarget);
			$manager->save();
        }

		return $this->redirectToRoute('traduction_target_index');
	}
}
