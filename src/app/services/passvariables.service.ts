import { Injectable } from '@angular/core';
import { Subject } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class PassvariablesService {

  private textComponentS = new Subject<string>();

  public textComponent$ = this.textComponentS.asObservable();

  constructor() { }

  public textComponentChange(tc: string): void{
    this.textComponentS.next(tc);
  }
}
