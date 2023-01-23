import { HttpClient } from "@angular/common/http";
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
}