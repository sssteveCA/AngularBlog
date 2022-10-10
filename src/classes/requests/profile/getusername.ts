import { HttpClient } from "@angular/common/http";
import GetUsernameInterface from "src/interfaces/requests/profile/getusername.interface";

export class getUsername{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: GetUsernameInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }


    get token_key(){return this._token_key;}
    get url(){return this._url;}
}