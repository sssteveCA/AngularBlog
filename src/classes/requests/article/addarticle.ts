import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Article } from "src/app/models/article.model";
import { Messages } from "src/constants/messages";
import AddArticleInterface from "src/interfaces/requests/article/addarticle.interface";

export default class AddArticle{
    private _article: Article;
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: AddArticleInterface){
        this._article = data.article;
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get article(){return this._article;}
    get http(){return this._http;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async createArticle(): Promise<object>{
        let response: object = {};
        try{
            let addarticle_values: object = {
                article: this._article,
                token_key: this._token_key
            };
            await this.createArticlePromise(addarticle_values).then(res => {
                console.log(res);
                response = JSON.parse(res);
                console.log(response);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
                done: false,
                msg: Messages.ARTICLENEW_ERROR
            };
        }
        return response;
    }

    private async createArticlePromise(createData: object): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders().set('Content-Type','application/json').set('Accept','application/json');
            this._http.post(this.url,createData,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            });
        });
    }
}