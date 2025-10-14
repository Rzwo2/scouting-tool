<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Domain\Statistic\Import\Model\ImportTypeModel;
use App\Form\Statistic\Import\StatisticImportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/statistic/import-form.html.twig')]
class StatisticImportForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?ImportTypeModel $formData = null;

    protected function instantiateForm(): FormInterface
    {
        dd('YES');

        return $this->createForm(StatisticImportType::class, $this->formData);
    }
}
