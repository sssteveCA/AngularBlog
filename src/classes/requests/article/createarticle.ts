import { HttpClient } from "@angular/common/http";
import { Article } from "src/app/models/article.model";
import CreateArticleInterface from "src/interfaces/requests/article/createarticle.interface";

export default class CreateArticle{
    private _article: Article;
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: CreateArticleInterface){
        this._article = data.article;
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get article(){return this._article;}
    get http(){return this._http;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}
}