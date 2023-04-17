import { Component, Input, OnInit, OnChanges, SimpleChanges } from '@angular/core';

@Component({
  selector: 'app-history-item',
  templateUrl: './history-item.component.html',
  styleUrls: ['./history-item.component.scss']
})
export class HistoryItemComponent implements OnInit, OnChanges {

  @Input() id: string;
  @Input() date: Date;
  @Input() description: string;
  @Input() title: string;
  dateString: string;

  constructor() { }

  ngOnChanges(changes: SimpleChanges): void {
    //console.log(changes);
  }

  ngOnInit(): void {
    this.dateString = this.date.toLocaleString('it-It',{timeZone: 'UTC'});
  }

}
