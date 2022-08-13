import { HttpClient } from "@angular/common/http";

export default interface GetArticlesInterface{
    http: HttpClient;
    token_key: string;
    url: string;
}