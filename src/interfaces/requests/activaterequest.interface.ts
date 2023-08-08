import { HttpClient } from "@angular/common/http";

export default interface ActivateRequestInterface{
    emailVerif: string|null;
    http: HttpClient;
    url: string;
}