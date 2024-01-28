import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import UpdateCommentInterface from "src/interfaces/requests/article/comment/updatecomment.interface";

export default class UpdateComment{
    private _comment_id: string;
    private _http: HttpClient;
    private _new_comment: string;
    private _old_comment: string;
    private _permalink: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateCommentInterface){
        this._comment_id = data.comment_id;
        this._http = data.http;
        this._new_comment = data.new_comment;
        this._old_comment = data.old_comment;
        this._permalink = data.permalink;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get comment_id(){return this._comment_id;}
    get http(){return this._http;}
    get new_comment(){return this._new_comment;}
    get old_comment(){return this._old_comment;}
    get permalink(){return this._permalink;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async updateComment(): Promise<object>{
        let response: object = {};
        try{
            const commentupdate_values: object = {
                new_comment: this._new_comment,
                old_comment: this._old_comment
            };
            await this.updateCommentPromise(commentupdate_values).then(res => {
                response = JSON.parse(res);
            }).catch(err => {
                throw err;
            });
        }catch(err){
            response = {
                done: false,
                msg: Messages.COMMENTUPDATE_ERROR
            };
        }
        return response;
    }

    private async updateCommentPromise(updateData: object): Promise<string>{
        return await new Promise<string>((resolve,reject)=>{
            const headers = new HttpHeaders({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'AngularBlogAuth': this._token_key
            });

            this._http.put(`${this._url}/${this._permalink}/comments/${this._comment_id}`,updateData,{headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
              })
        });
    }
}