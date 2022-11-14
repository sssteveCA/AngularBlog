import { HttpClient } from '@angular/common/http';
import ContactsRequestInterface from '../../interfaces/requests/constactsrequest.interface'

export default class ContactsRequest{
    private _email: string;
    private _http: HttpClient;
    private _subject: string;
    private _body: string;
    private _url: string;

    constructor(data: ContactsRequestInterface){
        this._body = data.body;
        this._email = data.email;
        this._http = data.http;
        this._subject = data.subject;
        this._url = data.url;
    }

    get body(){ return this._body; }
    get email(){ return this._email; }
    get subject(){ return this._subject; }
    get url(){ return this._url; }
    
}