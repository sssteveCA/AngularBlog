import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import DeleteProfileInterface from "src/interfaces/requests/profile/deleteprofile.interface";

export default class DeleteProfile{
    private _conf_password: string;
    private _http: HttpClient;
    private _password: string;
    private _token_key: string;
    private _url: string;
    private _errno: number = 0;
    private _error: string|null = null;

    public static ERR_REQUEST: number = 1;

    private static ERR_REQUEST_MSG: string = "Errore durante l'esecuzione della richiesta";

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
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            case DeleteProfile.ERR_REQUEST:
                this._error = DeleteProfile.ERR_REQUEST_MSG;
                break;
            default:
                this._error = null;
                break;
        }
        return this._error;
    }

    public async deleteProfile(): Promise<object>{
        let response: object = {}; 
        try{
            let deleteProfile_values: object = {
                conf_password: this._conf_password,
                password: this._password
            };
            await this.deleteProfilePromise(deleteProfile_values).then(res => {
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            })
        }catch(err){
            if(err instanceof HttpErrorResponse){
                const errorString: string = err.error as string;
                const errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }
            else{
                response[Keys.MESSAGE] = Messages.DELETEACCOUNT_ERROR;
            }
        }
        return response;
    }

    private async deleteProfilePromise(dp: object): Promise<string>{
        return await new Promise<string>((resolve, reject) => {
            const headers = new HttpHeaders()
                .set('Content-Type','application/json')
                .set('Accept','application/json')
                .set(Keys.AUTH,this._token_key);
            this._http.post(this._url,dp,{headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
    }
}