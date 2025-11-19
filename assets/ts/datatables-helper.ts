import { Api, ApiColumnMethods, ConfigLanguage } from "datatables.net-dt";

export class DatatablesHelper {
        public static languageGerman: ConfigLanguage = {
                "decimal": ",",
                "emptyTable": "Keine Daten vorhanden",
                "info": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen",
                "infoEmpty": "Zeige 0 bis 0 von 0 Einträgen",
                "infoFiltered": "(gefiltert von insgesamt _MAX_ Einträgen)",
                "infoPostFix": "",
                "thousands": ".",
                "lengthMenu": "_MENU_ Einträge pro Seite",
                "loadingRecords": "Lädt...",
                "processing": "",
                "search": "Suche:",
                "zeroRecords": "Keine passenden Einträge vorhanden",
                "paginate": {
                        "first": "Erste",
                        "last": "Letzte",
                        "next": "Vor",
                        "previous": "Zurück"
                },
                "aria": {
                        "orderable": "Sortierung dieser Spalte",
                        "orderableReverse": "Umgekehrte Sortierung dieser Spalte"
                }
        };

        public static addColumnFilterSelection($dataTable: Api<any>, columnName: string, placeholder: string = ''): void {
                let $column = $dataTable.column(`${columnName}:name`);
                let $select = $(`<select id="select-${columnName}">`)
                        .append(new Option(placeholder, ''));
                this.addOnChangeEventForColumn($column, $select, true);

                $column.data().unique().sort().each(function (option: string, _i: any) {
                        $select.append(new Option(option));
                });

                $(`<div id="select-${columnName}-container">`)
                        .append($select)
                        .appendTo($('.dt-length').parent());
        }
        public static addOnChangeEventForColumn(column: ApiColumnMethods<any>, $element: JQuery<HTMLElement>, exactMatch: boolean = false): void {
                $element.on('change', (event: JQuery.ChangeEvent) => {
                        column.search($(event.target).val(), { exact: exactMatch }).draw();
                })
        }
}
