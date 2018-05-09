import {AfterViewInit, Component, EventEmitter} from '@angular/core';
import {ElementRef, ViewChild, Input, Output}   from '@angular/core';

import {AnimationPlayerOptionsModel, PointModel} from '../../@base';
import {IAnimationPlayer}                        from '../../@base';
import {CanvasAnimationService}                  from '../../services/canvas-animation.service';

@Component({
    selector   : 'app-canvas-screensaver',
    templateUrl: './canvas-screensaver.component.html',
    styleUrls  : ['./canvas-screensaver.component.scss'],
    providers  : [
        CanvasAnimationService,
    ]
})
export class CanvasScreensaverComponent implements AfterViewInit {

    /**
     * Common options of animation player.
     */
    @Input()
    playerOptions: AnimationPlayerOptionsModel;

    /**
     * Event will be triggered when animation player will be initialized.
     */
    @Output()
    playerInit: EventEmitter<IAnimationPlayer> = new EventEmitter();

    /**
     * Canvas element.
     */
    @ViewChild('viewport')
    viewportElementRef: ElementRef<HTMLCanvasElement>;

    /**
     * Animation player object.
     */
    protected _animationPlayer: IAnimationPlayer;

    /**
     * Constructor.
     */
    constructor(protected canvasAnimation: CanvasAnimationService) {
    }

    /**
     * A callback method that is invoked immediately after
     * Angular has completed initialization of a component's view.
     * It is invoked only once when the view is instantiated.
     */
    ngAfterViewInit(): void {

        this._initComponent();
    }

    /**
     * Initializes component.
     *
     * @private
     */
    protected _initComponent() {

        // Prepare player
        this._animationPlayer = this.canvasAnimation.preparePlayer(this.playerOptions, this.viewportElementRef);

        // Trigger event
        this.playerInit.emit(this._animationPlayer);
    }

    /**
     * Returns canvas size (width and height).
     * @private
     */
    protected _getCanvasSize(): number {

        return this.playerOptions.maxViewportSize;
    }

    /**
     * Handles mouse move event (throttled).
     *
     * @param event
     * @private
     */
    // @throttle(300)
    protected _onMouseMove(event: MouseEvent) {

        const screenPoint = this._calculateCanvasScreenPoint(event.clientX, event.clientY);

        this._animationPlayer.setTouchPoint(screenPoint);
    }

    /**
     * Calculates correct screen point against to full canvas size.
     *
     * @param canvasClientX     Position relative to the visible canvas part.
     * @param canvasClientY     Position relative to the visible canvas part.
     * @private
     */
    protected _calculateCanvasScreenPoint(canvasClientX, canvasClientY): PointModel {

        const canvasSize      = this._getCanvasSize(),
              invisibleWidth  = canvasSize - window.innerWidth,
              invisibleHeight = canvasSize - window.innerHeight;

        return new PointModel(
            Math.round(canvasClientX + invisibleWidth / 2),
            Math.round(canvasClientY + invisibleHeight / 2),
            0);
    }
}
