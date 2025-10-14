<?php

namespace App\Controller\Statistic;

use App\Domain\Statistic\Import\ImportService;
use App\Form\Statistic\Import\StatisticImportType;
use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: 'statistic')]
final class StatisticController extends AbstractController
{
    #[Route('/', name: 'app_statistic')]
    public function index(): Response
    {
        return $this->render('statistic/index.html.twig', [
            'controller_name' => 'StatisticController',
        ]);
    }

    #[Route('/statistic/table', name: 'statistic_table')]
    public function getTable(): JsonResponse
    {
        return new JsonResponse();
    }

    #[Route(path: '/import', name: 'statistic_import', methods: ['GET', 'POST'])]
    public function importAction(Request $request, TeamRepository $teamRepository, ImportService $importService): Response
    {
        $form = $this->createForm(StatisticImportType::class);
        if (Request::METHOD_POST === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $importService->handleImport($form->getData());
                    $this->addFlash('success', 'Daten erfolgreich importiert');
                } catch (\Throwable $e) {
                    $this->addFlash('error', $e->getMessage());
                }

                return $this->redirect('/statistic/import');
            }
        }

        return $this->render('statistic/import/index.html.twig', ['form' => $form, 'formData' => $form->getData()]);
    }
}
