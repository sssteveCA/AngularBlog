import { HttpClient, HttpErrorResponse, HttpHandler, HttpHeaders } from '@angular/common/http';
import { Messages } from 'src/constants/messages';
import { ContactsParams } from 'src/constants/types';
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

    public async contactsRequest(): Promise<object>{
        let response: object = {};
        try{
            await this.contactsRequestPromise().then(res => {
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(e){
            response = { done: false };
            if(e instanceof HttpErrorResponse){
                let errorString: string = e.error as string;
                let errorBody: object = JSON.parse(errorString);
                response['msg'] = errorBody['msg'];
            }
            else{
                response['msg'] = Messages.CONTACTS_ERROR;
            }
        }
        return response;
    }

    private async contactsRequestPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const postData: ContactsParams = {
                body: this._body, email: this._email, subject: this._subject
            };
            const headers: HttpHeaders = new HttpHeaders({
                'Accept': 'application/json', 'Content-Type': 'application/json'
            });
            this._http.post(this._url, postData, { headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            }, error => {
                reject(error);
            });
        });
    }
    
}