import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-email',
  templateUrl: './email.component.html',
  styleUrls: ['./email.component.scss']
})
export class EmailComponent implements OnInit {

  email: string|null = "";
  @Input() emailObject: object;

  constructor() { }

  ngOnInit(): void {
  }

}
