import { HttpClient } from "@angular/common/http";

export default interface AddCommentInterface{
    comment_text: string;
    http: HttpClient;
    permalink: string;
    token_key: string;
    url: string;
}