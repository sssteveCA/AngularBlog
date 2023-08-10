import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-inline-spinner',
  templateUrl: './inline-spinner.component.html',
  styleUrls: ['./inline-spinner.component.scss']
})
export class InlineSpinnerComponent implements OnInit {

  @Input() showSpinner: boolean;

  @Input() spinnerId: string;

  constructor() { }

  ngOnInit(): void {
  }

}
