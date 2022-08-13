import GetArticlesInterface from "src/interfaces/requests/article/getarticles.interface";


export default class GetArticles{
    private _full_url: string;
    private _token_key: string;
    private _url: string;

    constructor(data: GetArticlesInterface){
        this._token_key = data.token_key;
        this._url = data.url;
        this._full_url = this._url+'?token_key='+this._token_key;
    }

    get token_key(){return this._token_key;}
    get url(){return this._url;}
}