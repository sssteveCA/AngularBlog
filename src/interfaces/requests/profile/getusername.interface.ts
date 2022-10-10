import { HttpClient } from "@angular/common/http";

export default interface GetUsernameInterface{
    http: HttpClient;
    token_key: string;
    url: string;
}