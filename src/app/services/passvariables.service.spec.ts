import { TestBed } from '@angular/core/testing';

import { PassvariablesService } from './passvariables.service';

describe('PassvariablesService', () => {
  let service: PassvariablesService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(PassvariablesService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
