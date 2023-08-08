import { HttpClient, HttpHeaders } from "@angular/common/http";
import ActivateRequestInterface from "src/interfaces/requests/activaterequest.interface";


/**
 * Account activation request class
 */
export default class ActivateRequest{
    private _emailVerif: string|null;
    private _http: HttpClient;
    private _url: string;

    constructor(data: ActivateRequestInterface){
        this._emailVerif = data.emailVerif;
        this._http = data.http;
        this._url = data.url;
    }

    get emailVerif(){ return this._emailVerif; }
    get http(){ return this._http; }
    get url(){ return this._url; }

    public async activate(): Promise<object>{
        let response: object = {}
        try{
            await this.activatePromise().then(res => {
                response = JSON.parse(res)
            }).catch(err => {
                throw err;
            })
        }catch(e){
            response = { status: -2 }
        }
        return response;
    }

    private async activatePromise(): Promise<string>{
        return await new Promise<string>((resolve, reject) => {
            let fullUrl: string = `${this._url}`;
            if(this._emailVerif != null)fullUrl += `?emailVerif=${this._emailVerif}`
            const headers: HttpHeaders = new HttpHeaders({
                'Accept': 'application/json'
            });
            this._http.get(fullUrl,{ headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error)
            });
        });
    }
}