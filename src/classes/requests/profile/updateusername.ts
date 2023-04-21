import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import UpdateUsernameInterface from "src/interfaces/requests/profile/updateusername.interface";

export default class UpdateUsername{
    private _http: HttpClient;
    private _new_username: string;
    private _password: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateUsernameInterface){
        this._http = data.http;
        this._new_username = data.new_username;
        this._password = data.password;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get new_username(){return this._new_username;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    /**
     * Update the current logged username
     * @returns 
     */
    public async updateUsername(): Promise<object>{
        let response: object = {};
        try{
            const updateusername_values: object = {
                new_username: this._new_username, password: this._password
            };
            await this.updateUsernamePromise(updateusername_values).then(res => {
                response = JSON.parse(res);
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
                response[Keys.MESSAGE] = Messages.EDITUSERNAME_ERROR;
            }
        }
        return response;
    }

    private async updateUsernamePromise(uu: object):Promise<string>{
        let promise = await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders()
                .set('Content-Type', 'application/json')
                .set('Accept', 'application/json')
                .set(Keys.AUTH, this._token_key);
            this._http.put(this._url, uu, {headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
        return promise;
    }
}