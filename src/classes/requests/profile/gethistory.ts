import { HttpClient, HttpHeaders } from "@angular/common/http";
import HistoryInterface from "src/interfaces/requests/profile/gethistory.interface";

export default class GetHistory{
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
            case GetHistory.ERR_REQUEST:
                this._error = GetHistory.ERR_REQUEST_MSG;
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
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            })
        }catch(e){
            this._errno = GetHistory.ERR_REQUEST;
            response = {done: false}
        }
        return response;
    }

    private async historyPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            this._http.get(`${this._url}`,{
                headers: new HttpHeaders({
                    'AngularBlogAuth': this._token_key
                }),
                responseType: 'text'
            }).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
    }
}