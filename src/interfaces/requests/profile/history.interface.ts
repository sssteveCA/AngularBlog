import { HttpClient } from "@angular/common/http";

export default interface HistoryInterface{
    http: HttpClient;
    token_key: string;
    url: string;
}