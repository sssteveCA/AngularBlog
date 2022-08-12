import { HttpClient } from "@angular/common/http";
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
                'token_key': this._token_key,
            }
        }catch(err){

        }
        return response;
    }
}