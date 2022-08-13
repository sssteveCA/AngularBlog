import { HttpClient } from "@angular/common/http";
import SubscribeRequestInterface from "src/interfaces/subscriberequest.interface";

export default class SubscribeRequest{
    private _confPwd: string;
    private _email: string;
    private _http: HttpClient;
    private _name: string;
    private _password: string;
    private _surname: string;
    private _url: string;
    private _username: string;

    constructor(data: SubscribeRequestInterface){
        this._confPwd = data.confPwd;
        this._email = data.email;
        this._http = data.http;
        this._name = data.name;
        this._password = data.password;
        this._surname = data.surname;
        this._url = data.url;
        this._username = data.username;
    }

    get confPwd(){return this._confPwd;}
    get email(){return this._email;}
    get name(){return this._name;}
    get password(){return this._password;}
    get surname(){return this._surname;}
    get url(){return this._url;}
    get username(){return this._username;}
}