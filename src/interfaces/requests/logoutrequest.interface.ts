import { HttpClient } from "@angular/common/http";

export default interface LogoutRequest{
    http: HttpClient;
    token_key: string;
    url: string;
}