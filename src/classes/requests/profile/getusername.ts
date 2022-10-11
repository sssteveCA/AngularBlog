import { HttpClient, HttpHeaders } from "@angular/common/http";
import GetUsernameInterface from "src/interfaces/requests/profile/getusername.interface";

export class getUsername{
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

    /**
     * Get the current logged username
     * @returns 
     */
    public async getUsername(): Promise<object>{
        let response: object = {};
        try{
            await this.getUsernamePromise().then( res => {
                //console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){

        }
        return response;
    }

    private async getUsernamePromise(): Promise<string>{
        let promise = await new Promise<string>((resolve, reject) => {
            const headers = new HttpHeaders().set('Content-Type','application/json').set('Accept','application/json');
            this._http.get(`${this.url}?token_key=${this._token_key}`, {headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            }, error => {
                reject(error);
            });
        });
        return promise;
    }
}