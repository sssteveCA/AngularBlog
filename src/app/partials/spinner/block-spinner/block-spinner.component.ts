import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-block-spinner',
  templateUrl: './block-spinner.component.html',
  styleUrls: ['./block-spinner.component.scss']
})
export class BlockSpinnerComponent implements OnInit {

  @Input() showSpinner: boolean;

  @Input() spinnerId: string;

  constructor() { }

  ngOnInit(): void {
  }

}
