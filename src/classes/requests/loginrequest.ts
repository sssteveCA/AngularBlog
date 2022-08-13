import { HttpClient } from "@angular/common/http";
import LoginRequestInterface from "src/interfaces/loginrequest.interface";

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
}