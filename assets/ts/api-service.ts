import { type AjaxResponse } from "datatables.net-dt";

export class ApiService {
        static async filterData<T>(url: string): Promise<T> {
                const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                        }
                });

                if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                }

                return await response.json()
        }
}

export function col<T>(name: keyof T): string {
        return name as string;
}

export interface AbstractDataTableResponse<T> extends AjaxResponse {
        data: T[];
}

export interface LinkModel {
        url: string;
        text: string;
        blank?: Boolean;
}
