import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import UpdateNamesInterface from "src/interfaces/requests/profile/updatenames.interface";

export default class UpdateNames{
    private _http: HttpClient;
    private _new_name: string;
    private _new_surname: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateNamesInterface){
        this._http = data.http;
        this._new_name = data.new_name;
        this._new_surname = data.new_surname;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get new_name(){ return this._new_name;}
    get new_surname(){ return this._new_surname;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    /**
     * Update the name and the surname of the current logged user
     * @returns 
     */
    public async updateUsername(): Promise<object>{
        let response: object = {};
        try{
            const updatenames_values: object = {
                new_name: this._new_name, new_surname: this._new_surname,
                token_key: this._token_key
            }
            await this.updateNamesPromise(updatenames_values).then(res => {
                console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = { done: false };
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorObject: object = JSON.parse(errorString);
                response['msg'] = errorObject['msg'];
            }
            else{
                response['msg'] = Messages.EDITUSERNAME_ERROR;
            }
        }
        return response;
    }

    private async updateNamesPromise(un: object): Promise<string>{
        let promise = await new Promise<string>((resolve, reject) => {
            const headers = new HttpHeaders().set('Content-Type', 'application/json').set('Accept', 'application/json');
            this._http.put(this._url, un, {
                headers: headers, responseType: 'text'
            }).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            });
        });
        return promise;
    }
}