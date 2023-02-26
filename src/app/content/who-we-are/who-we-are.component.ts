import { Component, OnInit } from '@angular/core';
import { PassvariablesService } from 'src/app/services/passvariables.service';

@Component({
  selector: 'app-who-we-are',
  templateUrl: './who-we-are.component.html',
  styleUrls: ['./who-we-are.component.scss']
})
export class WhoWeAreComponent implements OnInit {

  backlink: string = "/";
  text_container: string = "";

  constructor(private pvs: PassvariablesService) { 
    this.passVariablesObserver();
  }

  ngOnInit(): void {
  }

  passVariablesObserver(): void{
    this.pvs.textContainer$.subscribe(tc_class => {
      this.text_container = tc_class;
    });
  }

}
