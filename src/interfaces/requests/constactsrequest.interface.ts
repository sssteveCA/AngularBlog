import { HttpClient } from "@angular/common/http";

export default interface ContactsRequestInterface{
    body: string;
    email: string;
    http: HttpClient;
    subject: string;
    url: string;
}