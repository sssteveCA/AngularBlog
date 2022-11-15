import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-names',
  templateUrl: './names.component.html',
  styleUrls: ['./names.component.scss']
})
export class NamesComponent implements OnInit {

  userCookie: any = {};
  groupNames: FormGroup;
  showNamesSpinner: boolean = false;
  namesError: boolean = false;

  constructor(public http: HttpClient, public api: ApiService, public router: Router, public fb: FormBuilder) { 
    this.observeFromService();
    this.setFormGroupNames();
  }

  ngOnInit(): void {
  }

  private observeFromService(): void{
    this.api.userChanged.subscribe(userdata => {
      this.userCookie['token_key'] = userdata['token_key'];
      this.userCookie['username'] = userdata['username'];
    });
  }

  private setFormGroupNames(): void{
    this.groupNames = this.fb.group({
      'name': ['', Validators.compose([Validators.required, Validators.minLength(3)])],
      'surname': ['', Validators.compose([Validators.required,Validators.minLength(2)])]
    });
  }

}
