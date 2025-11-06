<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Domain\Statistic\Import\Model\ImportTypeModel;
use App\Domain\Statistic\Import\StatisticImportService;
use App\Form\Statistic\Import\StatisticImportType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/statistic/import-form.html.twig')]
#[IsGranted('ROLE_ADMIN')]
class StatisticImportForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public ?ImportTypeModel $formData = null;

    public function __construct(private readonly LoggerInterface $logger) {}

    #[LiveAction]
    public function save(EntityManagerInterface $entityManager, StatisticImportService $importService): Response
    {
        $this->submitForm();

        /** @var ImportTypeModel $importTypeModel */
        $importTypeModel = $this->getForm()->getData();

        try {
            $importService->handleImport($importTypeModel);
        } catch (\Throwable $throwable) {
            $this->logger->error($throwable->getMessage(), $throwable->getTrace());
            $this->addFlash('error', 'Beim Import ist ein Fehler aufgetreten. Kontaktiere den Admin');
        }

        return $this->redirectToRoute('statistic_overview');
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(StatisticImportType::class, $this->formData);
    }
}
