import { HttpClient } from "@angular/common/http";

export default interface ContactsRequestInterface{
    email: string;
    http: HttpClient;
    message: string;
    subject: string;
    url: string;
}