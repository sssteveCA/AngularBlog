import { HttpClient } from "@angular/common/http";
import DeleteProfileInterface from "src/interfaces/requests/profile/deleteprofile.interface";

export default class DeleteProfile{
    private _conf_password: string;
    private _http: HttpClient;
    private _password: string;
    private _token_key: string;
    private _url: string;

    constructor(data: DeleteProfileInterface){
        this._conf_password = data.conf_password;
        this._http = data.http;
        this._password = data.password;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get conf_password(){return this._conf_password;}
    get password(){return this._password;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}
}