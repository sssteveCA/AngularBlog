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
    let title,content,categ,tags;
    divCont = $('<div>');
      divCont.addClass('container');
        divR = $('<div>');
        divR.addClass('row');
          divC = $('<div>');
          divC.addClass('col col-md-8 offset-md-2');
            title = $('<h1>');
            title.addClass('title');
            title.text(data['title']);
            title.css('text-align','center');
          divC.append(title);
        divR.append(divC);
      divCont.append(divR);
        divR = $('<div>');
        divR.addClass('row');
          divC = $('<div>');
          divC.addClass('col col-md-8 offset-md-2 mt-3');
            content = $('<div>');
            content.text(data['content']);
          divC.append(content);
        divR.append(divC);
      divCont.append(divR);
       divR = $('<div>');
       divR.addClass('row');
        divC = $('<div>');
        divC.addClass('col-12 col-md-5 mt-3');
          categ = $('<p>');
          categ.text("Categorie: "+data['categories']);
          categ.css('font-weight','bold');
        divC.append(categ);
      divR.append(divC);
       divC = $('<div>');
       divC.addClass('col-12 col-md-5 offset-md-2 mt-3');
        tags = $('<p>');
        tags.text("Tag: "+data['tags']);
        tags.css('font-weight','bold');
       divC.append(tags);
      divR.append(divC);
    divCont.append(divR);
    $('#article').append(divCont);
  }



}
