import { HttpClient } from "@angular/common/http";
import HistoryInterface from "src/interfaces/requests/profile/history.interface";

export default class History{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;
    private _errno: number = 0;
    private _error: string|null = null;

    public static ERR_REQUEST: number = 1;

    private static ERR_REQUEST_MSG: string = "Errore durante l'esecuzione della richiesta";

    constructor(data: HistoryInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get url(){return this._url;}
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            case History.ERR_REQUEST:
                this._error = History.ERR_REQUEST_MSG;
                break;
            default:
                this._error = null;
                break;
        }
        return this._error;
    }

    public async history(): Promise<object>{
        let response: object = {};
        this._errno = 0;
        try{
            await this.historyPromise().then(res => {
                //console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            })
        }catch(e){
            this._errno = History.ERR_REQUEST;
            response = {done: false}
        }
        return response;
    }

    private async historyPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            this._http.get(`${this._url}?token_key=${this._token_key}`,{
                responseType: 'text'
            }).subscribe(res => {
                resolve(res);
            }, error => {
                reject(error);
            })
        });
    }
}