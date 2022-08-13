import { HttpClient } from "@angular/common/http";

export default interface GetArticleInterface{
    http: HttpClient;
    permalink: string;
    url: string;
}