import { ConfigLanguage } from "datatables.net-dt";

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
}
