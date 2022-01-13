import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AttivaComponent } from './attiva.component';

describe('AttivaComponent', () => {
  let component: AttivaComponent;
  let fixture: ComponentFixture<AttivaComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AttivaComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AttivaComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
