import { HttpClient, HttpErrorResponse, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import GetArticlesInterface from "src/interfaces/requests/article/getarticles.interface";


export default class GetArticles{
    private _http: HttpClient;
    private _token_key: string;
    private _url: string;

    constructor(data: GetArticlesInterface){
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async getArticles(): Promise<object>{
        let response: object = {};
        try{
            await this.getArticlesPromise().then(res => {
                //console.log(res);
                response = JSON.parse(res);
                //console.log(response);
            }).catch(err => {
                //console.warn(err);
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
                response[Keys.MESSAGE] = Messages.GETARTICLES_ERROR;
            }
        }
        return response;
    }

    private async getArticlesPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders()
                .set('Content-Type','application/json')
                .set('Accept','application/json')
                .set(Keys.AUTH,this._token_key);
            this._http.get(this._url,{headers: headers, responseType: 'text'}).subscribe(res => {
                resolve(res);
            },error => {
                reject(error);
            });
        });
    }
}