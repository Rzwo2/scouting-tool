<?php

declare(strict_types=1);

namespace App\Domain\Statistic;

use App\Domain\DataTable\AjaxData;
use App\Domain\DataTable\DataTablesHelper;
use App\Domain\Statistic\Overview\Model\StatisticResponseModel;
use App\Repository\PlayerGameStatisticRepository;

readonly class StatisticService
{
    public function __construct(
        private PlayerGameStatisticRepository $playerGameStatisticRepository,
    ) {}

    public function handleDataFetchRequest(AjaxData $ajaxData): StatisticResponseModel
    {
        $qb = $this->playerGameStatisticRepository->getQueryForStatisticDataByAjaxData($ajaxData);

        $amountTotal = DataTablesHelper::getAmountTotal($qb);
        DataTablesHelper::applyDataTablesAjaxData($qb, $ajaxData);
        $amountFiltered = DataTablesHelper::getAmountFiltered($qb);

        $data = $qb->getQuery()->getResult();

        return new StatisticResponseModel(
            data: $data,
            recordsFiltered: $amountFiltered,
            recordsTotal: $amountTotal,
            draw: $ajaxData->draw,
        );
    }
}
