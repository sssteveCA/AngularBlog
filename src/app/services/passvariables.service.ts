import { Injectable } from '@angular/core';
import { BehaviorSubject, Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PassvariablesService {

  private text_container = new BehaviorSubject('text-container');

  public textContainer$ = this.text_container.asObservable();

  constructor() { }

  public textContainerChange(tc: string): void{
    this.text_container.next(tc);
  }
}
