import { HttpClient, HttpHeaders } from "@angular/common/http";
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
        this._full_url = this._url+"?permalink="+this._permalink;
    }

    get full_url(){return this._full_url;}
    get http(){return this._http;}
    get permalink(){return this._permalink;}
    get url(){return this._url;}

    public async getArticle(): Promise<object>{
        let response: object = {};
        try{
            await this.getArticlePromise().then(res => {
                //console.log(res);
                response = JSON.parse(res);
                //console.log(response);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
              done: false
            };
        }
        return response;
    }

    private async getArticlePromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders().set('Content-Type', 'application/json').set('Accept', 'application/json');
            this._http.get(this._full_url,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            })
        });
    }
}