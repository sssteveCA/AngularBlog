import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Article } from "src/app/models/article.model";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
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
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async updateArticle(): Promise<object>{
        let response: object = {};
        try{
            const updatearticle_values: object = {
                article: this._article,
            };
            await this.updateArticlePromise(updatearticle_values).then(res => {
                //console.log(res);
                response = JSON.parse(res);
                //console.log(response);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = { done: false };
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }//if(err instanceof HttpErrorResponse){
            else{
                response[Keys.MESSAGE] = Messages.ARTICLEUPDATE_ERROR;
            }
        }
        return response;
    }

    private async updateArticlePromise(updateData: object): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders()
                .set('Content-Type', 'application/json')
                .set('Accept', 'application/json')
                .set(Keys.AUTH,this._token_key);
            this._http.put(this._url,updateData,{headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
    }
}