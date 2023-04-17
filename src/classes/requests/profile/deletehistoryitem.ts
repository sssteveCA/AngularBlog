import { HttpClient, HttpErrorResponse } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import DeleteHistoryItemInterface from "src/interfaces/requests/profile/deletehistoryitem.interface";

export default class DeleteHistoryItem{
    private _action_id: string;
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: DeleteHistoryItemInterface){
        this._action_id = data.action_id;
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get action_id(){return this._action_id;}
    get http(){return this._http;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async delete(): Promise<object>{
        let response: object = {}
        try{
            await this.deletePromise().then(res => {
                //console.log(res)
                response = JSON.parse(res)

            }).catch(err => {
                throw err;
            })
        }catch(err){
            response = { done: false };
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }
            else{
                response[Keys.MESSAGE] = Messages.HISTORYITEM_DELETE_ERROR;
            }
        }
        return response;
    }

    private async deletePromise(): Promise<string>{
        return await new Promise<string>((resolve,reject) => {
            this._http.delete(`${this.url}?action_id=${this._action_id}`,{
                headers: {
                    'AngularBlogAuth': this._token_key
                },
                responseType: 'text'
            }).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error)
            })
        });
    }
}