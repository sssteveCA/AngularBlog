import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import GetArticlesInterface from "src/interfaces/requests/article/getarticles.interface";


export default class GetArticles{
    private _full_url: string;
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: GetArticlesInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
        this._full_url = this._url+'?token_key='+this._token_key;
    }

    get full_url(){return this._full_url;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async getArticles(): Promise<object>{
        let response: object = {};
        try{
            await this.getArticlesPromise().then(res => {
                console.log(res);
                response = JSON.parse(res);
                console.log(response);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
                done: false,
                msg: Messages.DELETEARTICLE_ERROR
            };
        }
        return response;
    }

    private async getArticlesPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders().set('Content-Type','application/json').set('Accept','application/json');
            this._http.get(this._full_url,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            });
        });
    }
}