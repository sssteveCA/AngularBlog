import { Component, OnInit } from '@angular/core';
import { PassvariablesService } from 'src/app/services/passvariables.service';

@Component({
  selector: 'app-index',
  templateUrl: './index.component.html',
  styleUrls: ['./index.component.scss']
})
export class IndexComponent implements OnInit {

  text_container: string = "";

  constructor(private pvs: PassvariablesService) {
    this.passVariablesObserver();
  }

  ngOnInit(): void {
    
  }

  passVariablesObserver(): void{
    this.pvs.textContainer$.subscribe(tc_class => {
      console.log(`index component ${tc_class}`);
      this.text_container = tc_class;
    });
  }

}
