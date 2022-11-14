import { HttpClient } from "@angular/common/http";

export default interface SubscribeRequestInterface{
    confPwd: string;
    email: string;
    http: HttpClient;
    name: string;
    password: string;
    subscribed: number;
    surname: string;
    url: string;
    username: string;
}