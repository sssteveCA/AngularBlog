import { HttpClient } from "@angular/common/http";
import GetArticleInterface from "src/interfaces/requests/article/getarticle.interface";

export class GetArticle{
    private _http: HttpClient;
    private _permalink: string;
    private _url: string;

    constructor(data: GetArticleInterface){
        this._http = data.http;
        this._permalink = data.permalink;
        this._url = data.url;
    }

    get http(){return this._http;}
    get permalink(){return this._permalink;}
    get url(){return this._url;}
}