import { HttpClient } from "@angular/common/http";
import GetUserInfoInterface from "src/interfaces/requests/profile/getuserinfo.interface";

export default class GetUserInfo{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;
    private _errno: number = 0;
    private _error: string|null = null;

    public static ERR_REQUEST: number = 1;

    private static ERR_REQUEST_MSG: string = "Errore durante l'esecuzione della richiesta";

    constructor(data: GetUserInfoInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get url(){return this._url;}
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            case GetUserInfo.ERR_REQUEST:
                this._error = GetUserInfo.ERR_REQUEST_MSG;
                break;
            default:
                this._error = null;
                break;
        }
        return this._error;
    }

    public async getUserInfo(): Promise<object>{
        let response: object = {};
        this._errno = 0;
        try{
            await this.getUserInfoPromise().then(res => {
                //console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            })
        }catch(err){
            this._errno = GetUserInfo.ERR_REQUEST;
            response = {done: false}
        }
        return response;
    }

    private async getUserInfoPromise(): Promise<string>{
        let promise = await new Promise<string>((resolve,reject)=>{
            this._http.get(`${this._url}?token_key=${this._token_key}`,{
                responseType: 'text'
            }).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            })
        });
        return promise;
    }

}