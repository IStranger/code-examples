import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CanvasScreensaverComponent } from './canvas-screensaver.component';

describe('CanvasScreensaverComponent', () => {
  let component: CanvasScreensaverComponent;
  let fixture: ComponentFixture<CanvasScreensaverComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CanvasScreensaverComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CanvasScreensaverComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
