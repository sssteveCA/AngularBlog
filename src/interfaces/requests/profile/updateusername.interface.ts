import { HttpClient } from "@angular/common/http";

export default class UpdateUsernameInterface{
    http: HttpClient;
    new_username: string;
    token_key: string;
    url: string;
}