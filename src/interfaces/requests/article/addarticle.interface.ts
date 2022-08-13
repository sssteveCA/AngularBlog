import { HttpClient } from "@angular/common/http";
import { Article } from "src/app/models/article.model";

export default interface AddArticleInterface{
    article: Article;
    http: HttpClient;
    token_key: string;
    url: string;
}