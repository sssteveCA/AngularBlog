import { HttpClient } from "@angular/common/http";
import GetCommentsInterface from "src/interfaces/requests/article/comment/getcomments.interface";

export default class GetComments{
    private _full_url: string;
    private _http: HttpClient;
    private _permalink: string;
    private _token_key: string|null;
    private _url: string;

    constructor(data: GetCommentsInterface){
        this._http = data.http;
        this._permalink = data.permalink;
        if(data.hasOwnProperty('token_key'))
            this._token_key = data.token_key as string;
        else
            this._token_key = null;
        this._url = data.url;
    }

    get full_url(){return this._full_url;}
    get permalink(){return this._permalink;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

}