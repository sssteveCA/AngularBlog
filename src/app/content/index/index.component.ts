import { Component, OnInit } from '@angular/core';
import { PassvariablesService } from 'src/app/services/passvariables.service';

@Component({
  selector: 'app-index',
  templateUrl: './index.component.html',
  styleUrls: ['./index.component.scss']
})
export class IndexComponent implements OnInit {

  constructor(private pvs: PassvariablesService) {
     this.pvs.textComponent$.subscribe(tc_class => {
      console.log(`index component ${tc_class}`);
    });
    this.passVariablesObserver();
  }

  ngOnInit(): void {
  }

  passVariablesObserver(): void{
    
  }

}
