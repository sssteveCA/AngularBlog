import { HttpClient } from "@angular/common/http";

export default interface GetUserInfoInterface{
    http: HttpClient;
    token_key: string;
    url: string;
}