import { HttpClient } from "@angular/common/http";

export default interface UpdatePasswordInterface{
    http: HttpClient;
    conf_new_password: string;
    new_password: string;
    old_password: string;
    token_key: string;
    url: string;
}