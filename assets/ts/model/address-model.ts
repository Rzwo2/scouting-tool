export interface AddressModel {
        zip?: string;
        city?: string;
        street?: string;
        suffix?: string;
        number?: string;
}

export class AddressModelHelper {
        public static getAddressWithNewLine(addressModel: AddressModel): string {
                return this.getArray(addressModel).join("\n");
        }

        public static getAddressWithBRTag(addressModel: AddressModel): string {
                return this.getArray(addressModel).join("<br>");
        }

        private static getArray(addressModel: AddressModel): Array<string | null | undefined> {
                return [
                        [addressModel.zip, addressModel.city].join(' ') ?? null,
                        [addressModel.street, addressModel.suffix, addressModel.number].join(' ') ?? null,
                ];
        }
}

