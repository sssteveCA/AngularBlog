import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-attiva',
  templateUrl: './attiva.component.html',
  styleUrls: ['./attiva.component.scss']
})
export class AttivaComponent implements OnInit {

  status : number = 0;
  activationUrl: string = "http://localhost/angular/ex6/AngularBlog/src/assets/php/account/attiva.php";

  constructor(public http: HttpClient, private route: ActivatedRoute) {
    this.route.queryParams.subscribe(params =>{
      console.log(params);
      let urlParams = this.activationUrl+'?emailVerif='+params['emailVerif'];
      this.active(urlParams);
    });
    
   }

  ngOnInit(): void {
  }

  active(urlParams: any): void{
    //account activation
    console.log(urlParams);
    this.http.get(urlParams,{responseType: 'text'}).subscribe(resp => {
      console.log(resp);
      let rJson = JSON.parse(resp);
      this.status = rJson.status;
      console.log(this.status);
    });
  }

}
