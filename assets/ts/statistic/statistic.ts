import { Api, type ConfigColumns } from 'datatables.net';
import { DatatablesHelper } from '../datatables-helper.ts';
import DataTable from 'datatables.net-dt';
import { map } from 'jquery';

let $dataTable: Api<any> | null = null;

document.addEventListener('turbo:load', initializePage);

function initializePage() {
    const $importButton: JQuery = $('#import-button');
    const $importDialog: JQuery = $('#import-dialog');
    const importDialog = $importDialog.get(0) as HTMLDialogElement;
    $importButton.off('click').on('click', () => importDialog.showModal());

    $importDialog.off('click').on('click', (event) => {
        if (event.target == importDialog) {
            importDialog.close();
        }
    });

    if ($dataTable !== null) {
        $dataTable.destroy();
    }

    const $tableContainer = $('#table-container');
    if ($tableContainer.length === 0) return;

    let $table = $tableContainer.find('#statistic-table');
    if ($table.length === 0) {
        $table = $(`<table id="statistic-table"></table>`).appendTo($tableContainer);
    }

    const columns = getColumns();

    $dataTable = new DataTable('#statistic-table', {
        columns: columns,
        language: DatatablesHelper.languageGerman,
        layout: { topEnd: null, bottomStart: 'paging', bottom2Start: 'info', bottomEnd: null },
        order: [{ name: 'game', dir: 'asc' }, { name: 'team', dir: 'asc' }, { name: 'number', dir: 'asc' }],
        lengthMenu: [20, 30, 50],
        destroy: true,
        ajax: {
            url: $tableContainer.data('url'),
            method: 'POST',
        },
        serverSide: true,
        processing: true,
    });

    $table.find('thead').prepend($(`
        <tr>
            <th colspan=3>Spiel/Spieler</th>
            <th colspan=2>Punkte</th>
            <th colspan=5>Aufschlag</th>
            <th colspan=6>Annahme</th>
            <th colspan=7>Angriff</th>
            <th>Block</th>
        </tr>`)
    );

    // const tableScroll: null | HTMLDivElement = document.querySelector('.dt-scroll');
    // if (tableScroll) {
    //     tableScroll.addEventListener('mousemove', function (e: MouseEvent) {
    //         const rect = this.getBoundingClientRect();
    //         const scrollBody: null | HTMLElement = this.querySelector('.dt-scroll-body');
    //         if (scrollBody) {
    //             scrollBody.scrollLeft = Math.round((e.pageX - rect.x - rect.width / 4) * 100 / (rect.width / 2));
    //         }
    //     });
    // }

    DatatablesHelper.addOnChangeEventForColumn($dataTable.column(`team:name`), $('#select-team'), true);
    DatatablesHelper.addOnChangeEventForColumn($dataTable.column(`game:name`), $('#select-game'), true);
}

function getColumns(): ConfigColumns[] {
    return [
        {
            title: 'Team',
            name: 'team',
            data: 'team',
            visible: false,
        },
        {
            title: 'Spiel',
            name: 'game',
            data: 'game',
            visible: false,
        },
        {
            title: 'Nr',
            name: 'number',
            data: 'number',
        },
        {
            title: 'Pos',
            name: 'position',
            data: 'position',
            render: (pos: string) => {
                if (pos === 'Mittelblock') return 'MB';
                if (pos === 'Zuspiel') return 'Z';
                if (pos === 'Außenangriff') return 'AA';
                if (pos === 'Diagonal') return 'D';
                if (pos === 'Libero') return 'L';
                return 'U';
            },
        },
        {
            title: 'Name',
            name: 'name',
            data: 'name',
            className: 'dt-nowrap',
        },
        {
            title: 'Ges',
            name: 'pointsTotal',
            data: 'pointsTotal',
        },
        {
            title: 'W-L',
            name: 'pointsDiff',
            data: 'pointsDiff',
        },
        {
            title: 'Ges',
            name: 'serveAttempts',
            data: 'serveAttempts',
        },
        {
            title: 'Pkt ImS',
            name: 'serveSuccesss',
            data: 'serveSuccesss',
        },
        {
            title: 'Pkt ImS %',
            name: 'serveSuccesssPercent',
            data: 'serveSuccesssPercent',
        },
        {
            title: 'Fhl',
            name: 'serveErrors',
            data: 'serveErrors',
        },
        {
            title: 'Fhl %',
            name: 'serveErrorsPercent',
            data: 'serveErrorsPercent',
        },
        {
            title: 'Ges',
            name: 'receiveAttempts',
            data: 'receiveAttempts',
        },
        {
            title: 'Pos (Prf) %',
            name: 'receive3sPercent',
            data: 'receive3sPercent',
        },
        {
            title: 'Neg',
            name: 'receive1s',
            data: 'receive1s',
        },
        {
            title: 'Neg %',
            name: 'receive1sPercent',
            data: 'receive1sPercent',
        },
        {
            title: 'Fhl',
            name: 'receive0s',
            data: 'receive0s',
        },
        {
            title: 'Fhl %',
            name: 'receive0sPercent',
            data: 'receive0sPercent',
        },
        {
            title: 'Ges',
            name: 'attackAttempts',
            data: 'attackAttempts',
        },
        {
            title: 'Pkt',
            name: 'attackKills',
            data: 'attackKills',
        },
        {
            title: 'Pkt K1 %',
            name: 'attackKillsK1Percent',
            data: 'attackKillsK1Percent',
        },
        {
            title: 'Pkt K2 %',
            name: 'attackKillsK2Percent',
            data: 'attackKillsK2Percent',
        },
        {
            title: 'Pkt Ges %',
            name: 'attackKillsPercent',
            data: 'attackKillsPercent',
        },
        {
            title: 'Fhl',
            name: 'attackErrors',
            data: 'attackErrors',
        },
        {
            title: 'Fhl %',
            name: 'attackErrorsPercent',
            data: 'attackErrorsPercent',
        },
        {
            title: 'Pkt',
            name: 'blockSuccesss',
            data: 'blockSuccesss',
        },
    ];
}
