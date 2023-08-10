import { Component, EventEmitter, Input, OnChanges, OnInit, Output, SimpleChanges } from '@angular/core';
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

  constructor() { }

  ngOnChanges(changes: SimpleChanges): void {
    if(
      (changes['spinnerShow'].previousValue != changes['spinnerShow'].currentValue) || 
      (changes['i'].previousValue != changes['i'].currentValue)){
        this.showSpinnerBool = (this.spinnerShow == this.i);
    }
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
