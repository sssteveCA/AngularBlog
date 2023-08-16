import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Keys } from "src/constants/keys";
import { Messages } from "src/constants/messages";
import GetCommentsInterface from "src/interfaces/requests/article/comment/getcomments.interface";

export default class GetComments{
    private _full_url: string;
    private _http: HttpClient;
    private _permalink: string;
    private _token_key: string|null;
    private _url: string;

    constructor(data: GetCommentsInterface){
        this._http = data.http;
        this._permalink = data.permalink;
        if(data.hasOwnProperty('token_key'))
            this._token_key = data.token_key as string;
        else
            this._token_key = null;
        this._url = data.url;
        this.setFullUrl();
    }

    get full_url(){return this._full_url;}
    get permalink(){return this._permalink;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    private setFullUrl(): void{
        this._full_url = this._url+"?permalink="+this._permalink;
    }

    public async getComments(): Promise<object>{
        let response: object = {};
        try{
            await this.getCommentsPromise().then(res => {
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
                done: false,
                msg: Messages.COMMENTLIST_ERROR
            };
        };
        
        return response;
    }

    private async getCommentsPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            let headers: HttpHeaders|undefined = undefined;
            if(this._token_key != null){
                headers = new HttpHeaders({
                    'AngularBlogAuth': this._token_key
                });
            }
            this._http.get(this._full_url,{
                headers: headers, responseType: 'text'
            }).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
            });
        });
    }

}