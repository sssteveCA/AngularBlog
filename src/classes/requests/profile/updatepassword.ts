import { HttpClient } from "@angular/common/http";
import UpdatePasswordInterface from "src/interfaces/requests/profile/updatepassword.interface";

export default class UpdatePassword{
    private _http: HttpClient;
    private _conf_new_password: string;
    private _new_password: string;
    private _old_password: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdatePasswordInterface){
        this._conf_new_password = data.conf_new_password;
        this._http = data.http;
        this._new_password = data.new_password;
        this._old_password = data.old_password;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get conf_new_password(){return this._conf_new_password;}
    get new_password(){return this._new_password;}
    get old_password(){return this._old_password;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}
}