import { HttpClient } from "@angular/common/http";

export default interface GetCommentsInterface{
    http: HttpClient;
    permalink: string;
    token_key?: string;
    url: string;
}