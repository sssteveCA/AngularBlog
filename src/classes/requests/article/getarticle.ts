import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import GetArticleInterface from "src/interfaces/requests/article/getarticle.interface";

export class GetArticle{
    private _full_url: string;
    private _http: HttpClient;
    private _permalink: string;
    private _url: string;

    constructor(data: GetArticleInterface){
        this._http = data.http;
        this._permalink = data.permalink;
        this._url = data.url;
        this._full_url = this._url+"/"+this._permalink;
    }

    get full_url(){return this._full_url;}
    get permalink(){return this._permalink;}
    get url(){return this._url;}

    public async getArticle(): Promise<object>{
        let response: object = {};
        try{
            await this.getArticlePromise().then(res => {
                response = JSON.parse(res);
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
                response[Keys.MESSAGE] = Messages.GETARTICLE_ERROR;
            }
        }
        return response;
    }

    private async getArticlePromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            this._http.get(this._full_url,{responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
    }
}