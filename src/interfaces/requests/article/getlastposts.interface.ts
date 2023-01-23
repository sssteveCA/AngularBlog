import { HttpClient } from "@angular/common/http";

export default interface GetLastPostsInterface{
    http: HttpClient;
    url: string;
}