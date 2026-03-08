import { type AjaxResponse } from "datatables.net-dt";
export declare class ApiService {
    static filterData<T>(url: string): Promise<T>;
}
export declare function col<T>(name: keyof T): string;
export interface AbstractDataTableResponse<T> extends AjaxResponse {
    data: T[];
}
export interface LinkModel {
    url: string;
    text: string;
    blank?: Boolean;
}
