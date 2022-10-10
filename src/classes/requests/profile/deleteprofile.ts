import { HttpClient, HttpHeaders } from "@angular/common/http";
import DeleteProfileInterface from "src/interfaces/requests/profile/deleteprofile.interface";

export default class DeleteProfile{
    private _conf_password: string;
    private _http: HttpClient;
    private _password: string;
    private _token_key: string;
    private _url: string;

    constructor(data: DeleteProfileInterface){
        this._conf_password = data.conf_password;
        this._http = data.http;
        this._password = data.password;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get conf_password(){return this._conf_password;}
    get password(){return this._password;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async deleteProfile(): Promise<object>{
        let response: object = {}; 
        try{
            let deleteProfile_values: object = {
                conf_password: this._conf_password,
                password: this._password,
                token_key: this._token_key
            };
            await this.deleteProfilePromise(deleteProfile_values).then(res => {
                console.log(res);
                response = JSON.parse(res);
                //console.log(response);
            }).catch(err => {
                throw err;
            })
        }catch(err){

        }
        return response;
    }

    private async deleteProfilePromise(dp: object): Promise<string>{
        return await new Promise<string>((resolve, reject) => {
            const headers = new HttpHeaders().set('Content-Type','application/json').set('Accept','application/json');
            this._http.post(this._url,dp,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            }, error => {
                reject(error);
            });
        });
    }
}