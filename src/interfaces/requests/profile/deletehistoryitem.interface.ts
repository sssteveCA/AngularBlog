import { HttpClient } from "@angular/common/http";

export default interface DeleteHistoryItemInterface{
    action_id: string;
    http: HttpClient;
    token_key: string;
    url: string;
}