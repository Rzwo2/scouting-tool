export class AddressModelHelper {
    static getAddressWithNewLine(addressModel) {
        return this.getArray(addressModel).join("\n");
    }
    static getAddressWithBRTag(addressModel) {
        return this.getArray(addressModel).join("<br>");
    }
    static getArray(addressModel) {
        return [
            [addressModel.zip, addressModel.city].join(' ') ?? null,
            [addressModel.street, addressModel.suffix, addressModel.number].join(' ') ?? null,
        ];
    }
}
