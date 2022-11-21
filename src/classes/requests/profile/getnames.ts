import { HttpClient, HttpHeaders } from "@angular/common/http";
import GetUsernameInterface from "src/interfaces/requests/profile/getusername.interface";

export default class GetNames{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: GetUsernameInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async getNames(): Promise<object>{
        let response: object = {};
        try{
            await this.getNamesPromise().then(res => {
                //console.log(res);
                response = JSON.parse(res);
            })
        }catch(err){

        }
        return response;
    }

    private async getNamesPromise(): Promise<string>{
        let promise = await new Promise<string>((resolve,reject)=>{
            this._http.get(`${this._url}?token_key=${this._token_key}`,{
                responseType: 'text'
            }).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            });
        });
        return promise;
    }
}