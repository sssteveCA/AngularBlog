import { HttpClient } from "@angular/common/http";
import UpdateNamesInterface from "src/interfaces/requests/profile/updatenames.interface";

export default class UpdateNames{
    private _http: HttpClient;
    private _new_name: string;
    private _new_surname: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateNamesInterface){
        this._http = data.http;
        this._new_name = data.new_name;
        this._new_surname = data.new_surname;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get new_name(){ return this._new_name;}
    get new_surname(){ return this._new_surname;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}
}