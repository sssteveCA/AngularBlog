import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import LoginRequestInterface from "src/interfaces/loginrequest.interface";
import { Config } from "config";

export default class LoginRequest{
    private _http: HttpClient;
    private _password: string;
    private _url: string;
    private _username: string;

    constructor(data: LoginRequestInterface){
        this._http = data.http;
        this._password = data.password;
        this._url = data.url;
        this._username = data.username;
    }

    get password(){return this._password;}
    get url(){return this._url;}
    get username(){return this._username;}

    public async login(): Promise<object>{
        let response: object = {};
        try{
            const login_values: object = {
                username: this._username,
                password: this._password
            };
            await this.loginPromise(login_values).then(res => {
                //console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
                done: false,
                msg: Messages.LOGIN_ERROR
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
            this._http.post(this.url,loginData,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            });
        });
    }
}