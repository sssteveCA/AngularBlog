import { HttpClient } from "@angular/common/http";
import UpdateUsernameInterface from "src/interfaces/requests/profile/updateusername.interface";

export default class UpdateUsername{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateUsernameInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get token_key(){return this._token_key;}
    get url(){return this._url;}
}