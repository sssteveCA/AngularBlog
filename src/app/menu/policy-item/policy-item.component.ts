import { Component, ElementRef, Input, OnInit, ViewChild } from '@angular/core';
import { HasElementRef } from '@angular/material/core/common-behaviors/color';

@Component({
  selector: 'app-policy-item',
  templateUrl: './policy-item.component.html',
  styleUrls: ['./policy-item.component.scss']
})
export class PolicyItemComponent implements OnInit {

  @Input() menuColor: string;

  @ViewChild('policyItem',{static: false})policyItemEl: ElementRef<HTMLLIElement>;

  menuShown: boolean = false;

  constructor() { }

  ngOnInit(): void {
  }

  showHideContent(): void{
    this.policyItemEl.nativeElement.classList.toggle('show');
    let dropdownMenu: HTMLUListElement = this.policyItemEl.nativeElement.getElementsByClassName('dropdown-menu')[0] as HTMLUListElement;
    dropdownMenu.classList.toggle('show');
    if(!this.menuShown){
      this.policyItemEl.nativeElement.setAttribute('aria-expanded','true');
      dropdownMenu.setAttribute('data-bs-popper','static');
    }
    else{
      this.policyItemEl.nativeElement.setAttribute('aria-expanded','false');
      dropdownMenu.removeAttribute('data-bs-popper');
    }
    this.menuShown = !this.menuShown;
  }
}
