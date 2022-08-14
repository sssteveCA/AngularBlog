import { HttpClient } from "@angular/common/http";
import UpdateCommentInterface from "src/interfaces/requests/article/comment/updatecomment.interface";

export default class UpdateComment{
    private _comment_id: string;
    private _http: HttpClient;
    private _new_comment: string;
    private _old_comment: string;
    private _token_key: string;
    private _url: string;

    constructor(data: UpdateCommentInterface){
        this._comment_id = data.comment_id;
        this._http = data.http;
        this._new_comment = data.new_comment;
        this._old_comment = data.old_comment;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get comment_id(){return this._comment_id;}
    get http(){return this._http;}
    get new_comment(){return this._new_comment;}
    get old_comment(){return this._old_comment;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}
}