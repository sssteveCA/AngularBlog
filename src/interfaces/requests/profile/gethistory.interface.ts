import { HttpClient } from "@angular/common/http";

export default interface GetHistoryInterface{
    http: HttpClient;
    token_key: string;
    url: string;
}