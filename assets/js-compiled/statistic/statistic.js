import { DatatablesHelper } from "../datatables-helper.js";
import DataTable from 'datatables.net-dt';
let $dataTable = null;
document.addEventListener('turbo:load', initializePage);
function initializePage() {
    if ($dataTable !== null) {
        $dataTable.destroy();
    }
    const $tableContainer = $('#table-container');
    if ($tableContainer.length === 0)
        return;
    let $table = $tableContainer.find('#statistic-table');
    if ($table.length === 0) {
        $table = $(`<table id="statistic-table"></table>`).appendTo($tableContainer);
    }
    $dataTable = new DataTable('#statistic-table', {
        columns: getColumns(),
        language: DatatablesHelper.languageGerman,
        layout: { topEnd: null, bottomStart: 'paging', bottom2Start: 'info', bottomEnd: null },
        order: [{ name: 'game', dir: 'asc' }, { name: 'team', dir: 'asc' }, { name: 'number', dir: 'asc' }],
        lengthMenu: [20, 30, 50],
        destroy: true,
        ajax: {
            url: $tableContainer.data('url'),
            data: function (data) {
                let colTeam = data.columns.find(({ name }) => name === 'team');
                let colGame = data.columns.find(({ name }) => name === 'game');
                colTeam.search.value = String($('#select-team').val());
                colGame.search.value = String($('#select-game').val());
                return data;
            },
            method: 'POST',
        },
        serverSide: true,
        processing: true,
        drawCallback: function () {
            const api = this.api();
            highlightValues(api);
        },
    });
    $table.find('thead').prepend($(`
        <tr>
            <th colspan=3>Spiel/Spieler</th>
            <th colspan=2>Punkte</th>
            <th colspan=5>Aufschlag</th>
            <th colspan=6>Annahme</th>
            <th colspan=7>Angriff</th>
            <th>Block</th>
        </tr>`));
    $('#select-team').on('change', () => {
        $('#select-game').val('');
        $dataTable?.draw();
    });
    $('#select-game').on('change', () => $dataTable?.draw());
}
function highlightValues(api) {
    const highlightMap = new Map([
        ['serveSuccesss', 3],
        ['receive1s', -2],
        ['receive0s', -2],
        ['attackAttempts', 3],
    ]);
    api.columns().every(function () {
        const colName = this.name() ?? '';
        if (!highlightMap.has(colName))
            return;
        const data = this.data().unique().toArray().sort((a, b) => b - a);
        const highlight = highlightMap.get(colName);
        this.nodes().each((node) => {
            const $node = $(node);
            const cellValue = Number($node.text());
            if (!cellValue)
                return;
            for (let i = 0; i < Math.abs(highlight); i++) {
                if (cellValue === data[i]) {
                    $node.css('background-color', highlight > 0 ? 'green' : 'red');
                    break;
                }
            }
        });
    });
}
function getColumns() {
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
            render: (pos) => {
                if (pos === 'Mittelblock')
                    return 'MB';
                if (pos === 'Zuspiel')
                    return 'Z';
                if (pos === 'Außenangriff')
                    return 'AA';
                if (pos === 'Diagonal')
                    return 'D';
                if (pos === 'Libero')
                    return 'L';
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
            className: 'table-border-right',
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
            className: 'table-border-right',
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
            className: 'table-border-right',
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
            className: 'table-border-right',
        },
        {
            title: 'Pkt',
            name: 'blockSuccesss',
            data: 'blockSuccesss',
        },
    ];
}
