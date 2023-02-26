import { HttpClient } from "@angular/common/http";
import HistoryInterface from "src/interfaces/requests/profile/history.interface";

export default class History{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;
    private _errno: number = 0;
    private _error: string|null = null;

    constructor(data: HistoryInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get url(){return this._url;}
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            default:
                this._error = null;
                break;
        }
        return this._error;
    }
}