import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SvgRayComponent } from './svg-ray.component';

describe('SvgRayComponent', () => {
  let component: SvgRayComponent;
  let fixture: ComponentFixture<SvgRayComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SvgRayComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SvgRayComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
