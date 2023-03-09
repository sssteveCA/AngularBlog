import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-history-item',
  templateUrl: './history-item.component.html',
  styleUrls: ['./history-item.component.scss']
})
export class HistoryItemComponent implements OnInit {

  @Input() date: Date;
  @Input() description: string;
  @Input() title: string;
  dateString: string;

  constructor() { }

  ngOnInit(): void {
    this.dateString = this.date.toLocaleString('it-It',{timeZone: 'UTC'});
  }

}
