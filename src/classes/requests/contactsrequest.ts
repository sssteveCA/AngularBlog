import { HttpClient, HttpErrorResponse, HttpHandler, HttpHeaders } from '@angular/common/http';
import { Keys } from 'src/constants/keys';
import { Messages } from 'src/constants/messages';
import { ContactsParams } from 'src/constants/types';
import ContactsRequestInterface from '../../interfaces/requests/constactsrequest.interface'

export default class ContactsRequest{
    private _email: string;
    private _http: HttpClient;
    private _message: string;
    private _subject: string;
    private _url: string;

    constructor(data: ContactsRequestInterface){
        this._email = data.email;
        this._http = data.http;
        this._message = data.message;
        this._subject = data.subject;
        this._url = data.url;
    }

    get email(){ return this._email; }
    get message(){ return this._message; }
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
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }
            else{
                response[Keys.MESSAGE] = Messages.CONTACTS_ERROR;
            }
        }
        return response;
    }

    private async contactsRequestPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const postData: ContactsParams = {
                message: this._message, email: this._email, subject: this._subject
            };
            const headers: HttpHeaders = new HttpHeaders({
                'Accept': 'application/json', 'Content-Type': 'application/json'
            });
            this._http.post(this._url, JSON.stringify(postData), { headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
    }
    
}