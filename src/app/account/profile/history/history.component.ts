import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-history',
  templateUrl: './history.component.html',
  styleUrls: ['./history.component.scss']
})
export class HistoryComponent implements OnInit {

  title: string = "Cronologia azioni effettuate";

  constructor() { }

  ngOnInit(): void {
  }

}
