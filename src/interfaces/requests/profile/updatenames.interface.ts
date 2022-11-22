import { HttpClient } from "@angular/common/http";

export default interface UpdateNamesInterface{
    http: HttpClient;
    new_name: string;
    new_surname: string;
    token_key: string;
    url: string;
}