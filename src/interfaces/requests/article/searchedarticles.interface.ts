import { HttpClient } from "@angular/common/http";

export default interface SearchedArticlesInterface{
    http: HttpClient;
    query: string;
    url: string;
}