import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import { Article } from 'src/app/models/article.model';
import * as constants from 'src/constants/constants';

@Component({
  selector: 'app-article',
  templateUrl: './article.component.html',
  styleUrls: ['./article.component.scss']
})
export class ArticleComponent implements OnInit {

  article: string | null;
  //articles: Article = new Array();
  url: string = constants.articleView;

  constructor(public route: ActivatedRoute, public http: HttpClient, private router: Router) {
    this.route.paramMap.subscribe((params: ParamMap) => {
      this.article = params.get('article');
      console.log("Articolo: "+this.article);
      let permalink = (typeof this.article === "string")? this.article : "";
      this.getArticle(permalink);
    });
   }

   //get articles list from input query
   getArticle(permalink: string){
     const options = {responseType: 'string'};
     this.http.get(this.url+'?permalink='+permalink,{responseType: 'text'}).subscribe(res =>{
       //console.log(res);
       let rJson = JSON.parse(res);
       console.log(rJson);
       if(rJson['done'] == true)
        this.showArticle(rJson['article']);
      else{
        if(rJson['notfound'] == true){
          this.router.navigate(['/404']);
        }
      }//else{
     });
   }

  ngOnInit(): void {
  }

  //create HTML content from articles data
  showArticle(data: any){
    let divCont,divR,divC;
    let title,content,categ,tags,autInfo;
    let html = `
<div class="container">
    <div class="row">
      <div class="col col-md-8 offset-md-2">
        <h1 class="title text-center">${data['title']}</h1>
      </div>
    </div>
    <div class="row">
      <div class="col col-md-8 offset-md-2 my-5">
        <div>${data['content']}</div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 col-md-5 my-3">
        <p class="fw-bold">Categorie: ${data['categories']}</p>
        <p class="fw-bold">Tag: ${data['tags']}</p>
      </div>
      <div class="col-12 col-md-5 offset-md-2 my-3">
        <p class="fw-bold">Autore: </p>
      </div>
    </div>
</div> 
`;
    $('#article').html(html);
  }



}
