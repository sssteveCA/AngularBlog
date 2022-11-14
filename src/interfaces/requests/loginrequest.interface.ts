import { HttpClient } from "@angular/common/http";

export default interface LoginRequestInterface{
    http: HttpClient;
    password: string;
    url: string;
    username: string;
}