import { HttpClient } from "@angular/common/http";

export default interface DeleteCommentInterface{
    comment_id: string;
    http: HttpClient;
    token_key: string;
    url: string;
}