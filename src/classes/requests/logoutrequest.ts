import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
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

    public async logout(): Promise<object>{
        let response: object = {}
        this._errno = 0;
        try{
            await this.logoutPromise().then(res => {
                response = JSON.parse(res)
            }).catch(err => {
                throw err;
            })
        }catch(err){
            response = { done: false }
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }
            else{
                this._errno = LogoutRequest.ERR_REQUEST;
                response[Keys.MESSAGE] = ""
            } 
        }
        return response;
    }

    private async logoutPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders().set(Keys.AUTH,this._token_key)
            this._http.get(this._url,{
                headers: headers,
                responseType: 'text'
            }).subscribe({
                next: (res) => resolve(res),
                error: (err) => reject(err),
            })
        })
    }

}