import { HttpClient } from "@angular/common/http";
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
}