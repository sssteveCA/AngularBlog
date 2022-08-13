import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Article } from "src/app/models/article.model";
import UpdateArticleInterface from "src/interfaces/requests/article/updatearticle.interface";

export default class UpdateArticle{
    private _article: Article;
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateArticleInterface){
        this._article = data.article;
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get article(){return this._article;}
    get http(){return this._http;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async updateArticle(): Promise<object>{
        let response: object = {};
        try{
            const updatearticle_values: object = {
                article: this._article,
                token_key: this._token_key
            };
            const headers = new HttpHeaders().set('Content-Type', 'application/json').set('Accept', 'application/json');
            
        }catch(err){

        }
        return response;
    }
}