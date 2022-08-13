import { HttpClient } from "@angular/common/http";

export default interface SubscribeRequestInterface{
    confPwd: string;
    email: string;
    http: HttpClient;
    name: string;
    password: string;
    surname: string;
    url: string;
    username: string;
}