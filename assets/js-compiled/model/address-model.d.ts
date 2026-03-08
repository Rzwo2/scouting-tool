export interface AddressModel {
    zip?: string;
    city?: string;
    street?: string;
    suffix?: string;
    number?: string;
}
export declare class AddressModelHelper {
    static getAddressWithNewLine(addressModel: AddressModel): string;
    static getAddressWithBRTag(addressModel: AddressModel): string;
    private static getArray;
}
