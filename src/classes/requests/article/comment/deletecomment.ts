import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import DeleteCommentInterface from "src/interfaces/requests/article/comment/deletecomment.interface";

export default class DeleteComment{
    private _comment_id: string;
    private _http: HttpClient;
    private _permalink: string;
    private _token_key: string;
    private _url: string;

    constructor(data: DeleteCommentInterface){
        this._comment_id = data.comment_id;
        this._http = data.http;
        this._permalink = data.permalink;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get comment_id(){return this._comment_id;}
    get permalink(){return this._permalink;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async deleteComment(): Promise<object>{
        let response: object = {};
        try{
            await this.deleteCommentPromise().then(res => {
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
                done: false,
                msg: Messages.COMMENTDELETE_ERROR
            };
        }
        return response;
    }

    private async deleteCommentPromise(): Promise<string>{
        return await new Promise<string>((resolve,reject)=> {
            const headers: HttpHeaders = new HttpHeaders({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'AngularBlogAuth': this._token_key
              });
              this._http.delete(`${this._url}/${this._permalink}/comments/${this._comment_id}`,{headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
              });
        });
    }
}