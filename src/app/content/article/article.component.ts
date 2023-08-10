import { HttpClient, HttpParams } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import { Article } from 'src/app/models/article.model';
import { GetArticle } from 'src/classes/requests/article/getarticle';
import * as constants from 'src/constants/constants';
import { Keys } from 'src/constants/keys';
import { Messages } from 'src/constants/messages';
import GetArticleInterface from 'src/interfaces/requests/article/getarticle.interface';

@Component({
  selector: 'app-article',
  templateUrl: './article.component.html',
  styleUrls: ['./article.component.scss']
})
export class ArticleComponent implements OnInit {

  article: string | null;
  articleObj: Article;
  done: boolean = false;
  message: string;
  showSpinner: boolean = true;
  spinnerId: string = "news-spinner";
  //articles: Article = new Array();
  getArticle_url: string = constants.articleView;

  constructor(public route: ActivatedRoute, public http: HttpClient, private router: Router) {
    this.articleParams();
   }

  ngOnInit(): void {
    }

    articleParams(): void{
      this.route.paramMap.subscribe((params: ParamMap) => {
        this.article = params.get('article');
        let permalink = (typeof this.article === "string")? this.article : "";
        this.getArticle(permalink);
      });
    }


   /**
    * request to get the article data
    * @param permalink the permalink of the article
    */
   getArticle(permalink: string){
    const ga_data: GetArticleInterface = {
      http: this.http,
      permalink: permalink,
      url: this.getArticle_url
    };
    let ga: GetArticle = new GetArticle(ga_data);
    ga.getArticle().then(obj => {
      this.showSpinner = false;
      this.done = obj[Keys.DONE];
      if(obj[Keys.DONE] == true){
        this.articleObj = obj[Keys.DATA];
        //this.showArticle(obj[Keys.DATA]);
      }
      else{
        if(obj['notfound'] == true)
          this.router.navigateByUrl(constants.notFoundUrl); 
        else
          this.message = obj[Keys.MESSAGE];
      }//else{
    }).catch(err => {
      this.showSpinner = false;
      this.done = false;
      this.message = Messages.GETARTICLE_ERROR;
      //this.router.navigateByUrl(constants.notFoundUrl);
    });
   }
}
