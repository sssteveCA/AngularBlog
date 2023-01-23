import { HttpClient, HttpErrorResponse } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import GetLastPostsInterface from "src/interfaces/requests/article/getlastposts.interface";

export default class GetLastPosts{
    private _http: HttpClient;
    private _url: string;
    private _errno: number = 0;
    private _error: string|null = null;

    constructor(data: GetLastPostsInterface){
        this._http = data.http;
        this._url = data.url;
    }

    public static ERR_FETCH:number = 1;

    private static ERR_FETCH_MSG:string = "Errore durante l'esecuzione della richiesta";

    get url(){return this._url;}
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            case GetLastPosts.ERR_FETCH:
                this._error = GetLastPosts.ERR_FETCH_MSG;
                break;
            default:
                this._error = null;
                break;
        }
        return this._error;
    }

    public async getLastPosts(): Promise<object>{
        this._errno = 0;
        let response: object = {};
        try{
            await this.getLastPostsPromise().then(res => {
                //console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {done: false};
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response[Keys.MESSAGE] = errorBody[Keys.MESSAGE];
            }
            else{
                this._errno = GetLastPosts.ERR_FETCH;
                response[Keys.MESSAGE] = Messages.NEWS_ERROR;
            }
        }
        return response;
    }

    private async getLastPostsPromise(): Promise<string>{
        let promise = await new Promise<string>((resolve,reject)=>{
            this._http.get(this._url,{
                responseType: 'text'
            }).subscribe(res => {
                resolve(res);
            }, error => {
                reject(error);
            });
        });
        return promise;
    }
}