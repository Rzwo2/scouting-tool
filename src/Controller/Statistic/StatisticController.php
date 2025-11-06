<?php

namespace App\Controller\Statistic;

use App\Domain\DataTable\AjaxData;
use App\Domain\Statistic\StatisticService;
use App\Form\Statistic\Import\StatisticImportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: 'statistic')]
final class StatisticController extends AbstractController
{
    public function __construct(
        private readonly StatisticService $statisticService,
        private readonly SerializerInterface $serializer,
    ) {}

    #[Route('/', name: 'statistic_overview')]
    public function overviewAction(): Response
    {
        $form = $this->createForm(StatisticImportType::class);

        return $this->render('statistic/index.html.twig', [
            'controller_name' => 'StatisticController',
            'form' => $form,
            'formData' => $form->getData(),
            'dataUrl' => $this->generateUrl('statistic_table'),
        ]);
    }

    #[Route('/table', name: 'statistic_table')]
    public function getTable(#[MapRequestPayload] AjaxData $ajaxData): JsonResponse
    {
        $data = $this->statisticService->handleDataFetchRequest($ajaxData);

        return $this->json($data);
    }
}
