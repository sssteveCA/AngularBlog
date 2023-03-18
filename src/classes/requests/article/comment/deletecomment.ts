import { HttpClient, HttpHeaders } from "@angular/common/http";
import { Messages } from "src/constants/messages";
import DeleteCommentInterface from "src/interfaces/requests/article/comment/deletecomment.interface";

export default class DeleteComment{
    _comment_id: string;
    _http: HttpClient;
    _token_key: string;
    _url: string;

    constructor(data: DeleteCommentInterface){
        this._comment_id = data.comment_id;
        this._http = data.http;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get comment_id(){return this._comment_id;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}

    public async deleteComment(): Promise<object>{
        let response: object = {};
        try{
            const deletecomment_values: object = {
                'comment_id': this._comment_id,
            }
            await this.deleteCommentPromise(deletecomment_values).then(res => {
                //console.log(res);
                response = JSON.parse(res);
                //console.log(response);
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

    private async deleteCommentPromise(deleteData: object): Promise<string>{
        return await new Promise<string>((resolve,reject)=> {
            const headers: HttpHeaders = new HttpHeaders({
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'AngularBlogAuth': this._token_key
              });
              this._http.post(this._url,deleteData,{headers: headers, responseType: 'text'}).subscribe({
                next: (res) => resolve(res),
                error: (error) => reject(error) 
              });
        });
    }
}