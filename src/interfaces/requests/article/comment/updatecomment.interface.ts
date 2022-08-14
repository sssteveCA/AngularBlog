import { HttpClient } from "@angular/common/http";

export default interface UpdateCommentInterface{
    comment_id: string;
    http: HttpClient;
    new_comment: string;
    old_comment: string;
    token_key: string;
    url: string;
}