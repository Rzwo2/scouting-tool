import type { AbstractDataTableResponse, LinkModel } from "../../api-service";
import type { AddressModel } from "../../model/address-model";

export interface TeamResponseModel extends AbstractDataTableResponse<TeamModel> {

}

export interface TeamModel {
        name: string;
        address: AddressModel;
        options: Array<LinkModel>
}
