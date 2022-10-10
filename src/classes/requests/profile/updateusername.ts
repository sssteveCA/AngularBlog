import { HttpClient, HttpHeaders } from "@angular/common/http";
import UpdateUsernameInterface from "src/interfaces/requests/profile/updateusername.interface";

export default class UpdateUsername{
    private _http: HttpClient;
    private _new_username: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateUsernameInterface){
        this._http = data.http;
        this._new_username = data.new_username;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get new_username(){return this._new_username;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async updateUsername(): Promise<object>{
        let response: object = {};
        try{
            const updateusername_values: object = {
                new_username: this._new_username,
                token_key: this._token_key
            };
            await this.updateUsernamePromise(updateusername_values).then(res => {
                //console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){

        }
        return response;
    }

    private async updateUsernamePromise(uu: object):Promise<string>{
        let promise = await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders().set('Content-Type', 'application/json').set('Accept', 'application/json');
            this._http.put(this._url, uu, {headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            }, error => {
                reject(error);
            })
        });
        return promise;
    }
}