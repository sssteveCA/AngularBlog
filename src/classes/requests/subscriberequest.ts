import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import SubscribeRequestInterface from "src/interfaces/subscriberequest.interface";

export default class SubscribeRequest{
    private _confPwd: string;
    private _email: string;
    private _http: HttpClient;
    private _name: string;
    private _password: string;
    private _subscribed: number;
    private _surname: string;
    private _url: string;
    private _username: string;

    constructor(data: SubscribeRequestInterface){
        this._confPwd = data.confPwd;
        this._email = data.email;
        this._http = data.http;
        this._name = data.name;
        this._password = data.password;
        this._subscribed = data.subscribed;
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

    public async subscribe(): Promise<object>{
        let response: object = {};
        try{
            const subscribe_values: object = {
                confPwd: this._confPwd,
                email: this._email,
                name: this._name,
                password: this._password,
                subscribed: this._subscribed,
                surname: this._surname,
                username: this._username
            };
            await this.subscribePromise(subscribe_values).then(res => {
                console.log(res);
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

    private async subscribePromise(subscribeData: object): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers: HttpHeaders = new HttpHeaders({
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            });
            this._http.post(this.url,subscribeData,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            });
        });
    }
}