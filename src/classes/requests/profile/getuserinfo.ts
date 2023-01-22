import { HttpClient } from "@angular/common/http";
import GetUserInfoInterface from "src/interfaces/requests/profile/getuserinfo.interface";

export default class GetUserInfo{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: GetUserInfoInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async getUserInfo(): Promise<object>{
        let response: object = {};
        try{
            await this.getUserInfoPromise().then(res => {
                console.log(res);
                response = JSON.parse(res);
            })
        }catch(err){

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