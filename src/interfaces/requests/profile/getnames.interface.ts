import { HttpClient } from "@angular/common/http";

export default interface GetNamesInterface{
    http: HttpClient;
    token_key: string;
    url: string;
}