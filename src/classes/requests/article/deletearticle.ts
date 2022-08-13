import { HttpClient } from "@angular/common/http";
import DeleteArticleInterface from "src/interfaces/requests/article/deletearticle.interface";

export default class DeleteArticle{
    private _article_id: string;
    private _http: HttpClient;
    private _token_key: string;

    constructor(data: DeleteArticleInterface){
        this._article_id = data.article_id;
        this._http = data.http;
        this._token_key = data.token_key;
    }

    get article_id(){return this._article_id;}
    get token_key(){return this._token_key;}
}