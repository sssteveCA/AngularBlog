import { Component, OnDestroy, OnInit } from '@angular/core';
import { Subscription } from 'rxjs';
import { PassvariablesService } from 'src/app/services/passvariables.service';

@Component({
  selector: 'app-index',
  templateUrl: './index.component.html',
  styleUrls: ['./index.component.scss']
})
export class IndexComponent implements OnInit, OnDestroy {

  text_container: string = "";
  subscription: Subscription;

  constructor(private pvs: PassvariablesService) {
    
  }

  ngOnInit(): void {
    this.passVariablesObserver();
  }

  ngOnDestroy(): void {
    if(this.subscription) this.subscription.unsubscribe();
  }

  passVariablesObserver(): void{
    this.subscription = this.pvs.textContainer$.subscribe(tc_class => {
      this.text_container = tc_class;
    });
  }

}
