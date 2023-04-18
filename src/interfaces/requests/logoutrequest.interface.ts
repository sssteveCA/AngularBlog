import { HttpClient } from "@angular/common/http";

export default interface LogoutRequestInterface{
    http: HttpClient;
    token_key: string;
    url: string;
}