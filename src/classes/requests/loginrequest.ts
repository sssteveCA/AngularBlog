import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import LoginRequestInterface from "src/interfaces/requests/loginrequest.interface";
import { Config } from "config";
import { Keys } from "src/constants/keys";

export default class LoginRequest{
    private _http: HttpClient;
    private _password: string;
    private _url: string;
    private _username: string;
    private _errno: number = 0;
    private _error: string|null = null;

    public static ERR_REQUEST: number = 1;

    private static ERR_REQUEST_MSG: string = "Errore durante l'esecuzione della richiesta";

    constructor(data: LoginRequestInterface){
        this._http = data.http;
        this._password = data.password;
        this._url = data.url;
        this._username = data.username;
    }

    get password(){return this._password;}
    get url(){return this._url;}
    get username(){return this._username;}
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            case LoginRequest.ERR_REQUEST:
                this._error = LoginRequest.ERR_REQUEST_MSG;
                break;
            default:
                this._error = null;
                break;
        }
        return this._error;
    }

    public async login(): Promise<object>{
        let response: object = {};
        try{
            const login_values: object = {
                username: this._username,
                password: this._password
            };
            await this.loginPromise(login_values).then(res => {
                response = JSON.parse(res);
            }).catch(err => {
                console.warn(err);
                throw err;
            });
        }catch(err){
            this._errno = LoginRequest.ERR_REQUEST;
            response = { done: false };
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }//if(err instanceof HttpErrorResponse){
            else{
                response[Keys.MESSAGE] = Messages.LOGIN_ERROR;
            }
        }
        return response;
    }

    private async loginPromise(loginData: object): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers: HttpHeaders = new HttpHeaders({
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            });
            this._http.post(this.url,loginData,{headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
    }
}