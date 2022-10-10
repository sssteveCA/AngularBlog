import { HttpClient } from "@angular/common/http";

export default interface DeleteProfileInterface{
    conf_password: string;
    http: HttpClient;
    password: string;
    token_key: string;
    url: string;
}