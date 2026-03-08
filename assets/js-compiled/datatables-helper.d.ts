import { Api, ApiColumnMethods, ConfigLanguage } from "datatables.net-dt";
export declare class DatatablesHelper {
    static languageGerman: ConfigLanguage;
    static addColumnFilterSelection($dataTable: Api<any>, columnName: string, placeholder?: string): void;
    static addOnChangeEventForColumn(column: ApiColumnMethods<any>, $element: JQuery<HTMLElement>, exactMatch?: boolean): void;
}
