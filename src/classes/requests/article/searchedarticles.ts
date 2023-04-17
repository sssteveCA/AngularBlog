import { HttpClient, HttpErrorResponse, HttpParams } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import SearchedArticlesInterface from "src/interfaces/requests/article/searchedarticles.interface";

export default class SearchedArticles{
    private _http: HttpClient;
    private _query: string;
    private _url: string;
    private _errno: number = 0;
    private _error: string|null = null;

    public static ERR_FETCH:number = 1;

    private static ERR_FETCH_MSG:string = "Errore durante l'esecuzione della richiesta";

    constructor(data: SearchedArticlesInterface){
        this._http = data.http;
        this._query = data.query;
        this._url = data.url;
    }

    get query(){return this._query;}
    get url(){return this._url;}
    get errno(){return this._errno;}
    get error(){
        switch(this._errno){
            case SearchedArticles.ERR_FETCH:
                this._error = SearchedArticles.ERR_FETCH_MSG;
                break;
            default:
                this._error = null;
                break;
        }
        return this._error = null;
    }

    public async searchedArticles(): Promise<object>{
        this._errno = 0;
        let response: object = {};
        try{
            await this.searchedArticlesPromise().then(res => {
                console.log(res);
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = { done: false };
            if(err instanceof HttpErrorResponse){
                let errorString: string = err.error as string;
                let errorBody: object = JSON.parse(errorString);
                response = errorBody[Keys.MESSAGE];
            }
            else{
                this._errno = SearchedArticles.ERR_FETCH;
                response[Keys.MESSAGE] = Messages.ARTICLESEARCH_ERROR;
            }
        }
        return response;
    }

    private async searchedArticlesPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            let params: object = {query: this._query};
            this._http.post(this._url,params,{
                responseType: 'text',
            }).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            })
        });
        
    }

}