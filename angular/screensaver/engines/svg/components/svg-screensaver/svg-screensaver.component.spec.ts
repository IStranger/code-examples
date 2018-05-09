import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SvgScreensaverComponent } from './svg-screensaver.component';

describe('ScreensaverComponent', () => {
  let component: SvgScreensaverComponent;
  let fixture: ComponentFixture<SvgScreensaverComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [SvgScreensaverComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SvgScreensaverComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
