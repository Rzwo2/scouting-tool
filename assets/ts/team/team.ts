// import $ from "jquery";
// import type { TeamModel, TeamResponseModel } from "./model/teams-model";
// import type { ConfigColumns } from "datatables.net-dt";
// import { AddressModelHelper } from "../model/address-model";
// import { ApiService, col } from "../api-service";
//
// let $table: JQuery<HTMLElement> = $("#table-container");
//
// ApiService.filterData<TeamResponseModel>($table.data('filter-url')).then((data) => {
//     let table = $table.DataTable({
//         data: data.data,
//         columns: getColumns(),
//         pageLength: 25,
//         order: [[0, 'asc']],
//         language: {
//         },
//     });
// });
//
// function getColumns(): ConfigColumns[] {
//     return [
//         {
//             name: 'options',
//             title: 'Optionen',
//             render: (data: TeamModel) => data.options.map((option) => {
//                 return $('<a>').attr('href', option.url).text(option.text).attr('target', option.blank ? '_blank' : '').get(0)?.getHTML();
//             }).join('<br>')
//         },
//         {
//             data: col<TeamModel>('name'),
//             title: 'Name',
//         },
//         {
//             title: 'Adresse',
//             render: (data: TeamModel) => AddressModelHelper.getAddressWithBRTag(data.address)
//         },
//     ];
// }

