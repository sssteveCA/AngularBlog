import { HttpClient } from "@angular/common/http";
import LogoutRequestInterface from "src/interfaces/requests/logoutrequest.interface";

export default class LogoutRequest{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;
    private _errno: number = 0;
    private _error: string|null = null;

    public static ERR_REQUEST: number = 1;

    private static ERR_REQUEST_MSG: string = "Errore durante l'esecuzione della richiesta";

    constructor(data: LogoutRequestInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get token_key(){return this._token_key;}
    get url(){return this._url;}
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            case LogoutRequest.ERR_REQUEST:
                this._error = LogoutRequest.ERR_REQUEST_MSG;
                break;
            default:
                this._error = null;
                break;
        }
        return this._error;
    }

}