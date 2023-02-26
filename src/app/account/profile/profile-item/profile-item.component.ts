import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-profile-item',
  templateUrl: './profile-item.component.html',
  styleUrls: ['./profile-item.component.scss']
})
export class ProfileItemComponent implements OnInit {

  @Input() title: string;
  @Input() text: string;
  @Input() link: string;

  constructor() { }

  ngOnInit(): void {
  }

}
