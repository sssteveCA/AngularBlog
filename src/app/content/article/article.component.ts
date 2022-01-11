import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { Article } from 'src/app/models/article.model';

@Component({
  selector: 'app-article',
  templateUrl: './article.component.html',
  styleUrls: ['./article.component.scss']
})
export class ArticleComponent implements OnInit {

  article: string | null;
  //articles: Article = new Array();
  url: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/search_article.php";

  constructor(public route: ActivatedRoute, public http: HttpClient) {
    this.route.paramMap.subscribe((params: ParamMap) => {
      this.article = params.get('article');
      console.log("Articolo: "+this.article);
      let query = (typeof this.article === "string")? this.article : "";
      this.getArticle(query);
    });
   }

   //get articles list from input query
   getArticle(query: string){
     let params = new HttpParams().append('query',query);
     this.http.post(this.url,params).subscribe(res =>{
       console.log(res);
     });
   }

  ngOnInit(): void {
  }



}
