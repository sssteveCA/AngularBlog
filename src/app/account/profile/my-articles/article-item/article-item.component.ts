import { Component, EventEmitter, Input, OnChanges, OnInit, Output, SimpleChanges } from '@angular/core';
import { Router } from '@angular/router';
import { Article } from 'src/app/models/article.model';

@Component({
  selector: 'app-article-item',
  templateUrl: './article-item.component.html',
  styleUrls: ['./article-item.component.scss']
})
export class ArticleItemComponent implements OnInit, OnChanges {

  @Input() article: Article;
  @Input() blog_url: string;
  @Input() deleteArticle_url: string;
  @Input() editArticle_url: string;
  @Input() i: number;
  @Input() spinnerShow: number;
  @Output() deleteAction: EventEmitter<object> = new EventEmitter<object>();

  showSpinnerBool: boolean = false;
  spinnerId: string = "article-item-spinner"

  constructor(private router: Router) { }

  ngOnChanges(changes: SimpleChanges): void {
    if(!changes['spinnerShow'].firstChange)
      this.showSpinnerBool = (changes['spinnerShow'].currentValue == this.i)
  }

  ngOnInit(): void {
  }

  delete(): void{
    let data: object = {
      article_id: this.article.id,
      article_pos: $('input[name=article_pos]').val() as number
    }
    this.deleteAction.emit(data)
  }

}
