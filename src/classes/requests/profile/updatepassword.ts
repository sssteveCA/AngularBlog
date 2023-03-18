import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import UpdatePasswordInterface from "src/interfaces/requests/profile/updatepassword.interface";

export default class UpdatePassword{
    private _http: HttpClient;
    private _conf_new_password: string;
    private _new_password: string;
    private _old_password: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdatePasswordInterface){
        this._conf_new_password = data.conf_new_password;
        this._http = data.http;
        this._new_password = data.new_password;
        this._old_password = data.old_password;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get conf_new_password(){return this._conf_new_password;}
    get new_password(){return this._new_password;}
    get old_password(){return this._old_password;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async updatePassword(): Promise<object>{
        let response: object = {};
        try{
            const passwordUpdate_values: object = {
                conf_new_password: this._conf_new_password,
                new_password: this._new_password,
                old_password: this._old_password,
            };
            await this.updatePasswordPromise(passwordUpdate_values).then(res => {
                //console.log(res);
                response = JSON.parse(res);
                //console.log(response);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = { done: false };
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }//if(err instanceof HttpErrorResponse){
            else{
                response[Keys.MESSAGE] = Messages.EDITPASSWORD_ERROR;
            }
        }
        return response;
    }

    private async updatePasswordPromise(up: object):Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders()
                .set('Content-Type', 'application/json')
                .set('Accept', 'application/json')
                .set(Keys.AUTH, this._token_key);
            this._http.put(this._url,up,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            }, error => {
                reject(error);
            });
        });
    }
}