import { Component, Input, OnInit } from '@angular/core';
import { Article } from 'src/app/models/article.model';

@Component({
  selector: 'app-article-item',
  templateUrl: './article-item.component.html',
  styleUrls: ['./article-item.component.scss']
})
export class ArticleItemComponent implements OnInit {

  @Input() article: Article;
  @Input() blog_url: string;
  @Input() deleteArticle_url: string;
  @Input() editArticle_url: string;
  @Input() i: number;
  @Input() spinnerShow: number;

  constructor() { }

  ngOnInit(): void {
  }

}
