import { HttpClient } from "@angular/common/http";

export default interface DeleteArticle{
    article_id: string;
    http: HttpClient;
    token_key: string;
}