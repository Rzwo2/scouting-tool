import DataTable from 'datatables.net-dt';
import $ from 'jquery';

let table = $(`<table id="statistic-table"></table>`).appendTo($('#table-container'));

let $table = new DataTable(table, {
    ajax: '/statistic/table'
});
