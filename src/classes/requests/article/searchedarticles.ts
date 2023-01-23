import { HttpClient } from "@angular/common/http";
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

}