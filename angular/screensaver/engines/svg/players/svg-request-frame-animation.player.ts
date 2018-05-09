import {ElementRef, Renderer2} from '@angular/core';

import {AnimateService, TDrawFunction} from '@app/shared/modules/animate';
import {PointModel, CoordinateService} from '../@base';
import {RayToDrawModel}                from '../models/ray-to-draw.model';
import {AbstractSvgAnimationPlayer}    from './abstract-svg-animation.player';


/**
 * The player uses animation based on requestAnimationFrame()
 */
export class SvgRequestFrameAnimationPlayer extends AbstractSvgAnimationPlayer {

    /**
     * Constructor.
     */
    constructor(protected animateService: AnimateService,
                protected coordinateService: CoordinateService,
                protected renderer: Renderer2) {

        super();
    }

    /**
     * Starts animation.
     */
    startAnimation() {

        this.animateService.animate({
            duration         : 120 * 1000,
            infinite         : true,
            runOutsideAngular: true,
            timing           : this.animateService.getTimingFuncByAlias('ease-in-linear'),
            draw             : this._prepareDrawFunc(this.viewportElement, this.raysToDraw),
        });

    }

    /**
     * Set touch point.
     *
     * @param screenPoint
     */
    setTouchPoint(screenPoint: PointModel) {

        // Has no implementation.
    }

    /**
     * Prepares draw function. progress - relative progress [0,1].
     * @private
     */
    protected _prepareDrawFunc(svgElementRef: ElementRef, raysToDraw: RayToDrawModel[]): TDrawFunction {

        return (progress: number): void => {

            for (const rayToDraw of raysToDraw) {

                rayToDraw.position = this.options.progressFunc(rayToDraw.ray.initialPosition, progress);
                this._moveSvgRay(rayToDraw);
            }
        };
    }

    /**
     * Moves specified ray to current position (defined by {@link RayToDrawModel.position}).
     *
     * @param rayToDraw
     * @private
     */
    protected _moveSvgRay(rayToDraw: RayToDrawModel) {

        const point   = this.coordinateService.calculateCartesianPointFromPolar(rayToDraw.position);
        const offsetX = this._formatNumber(point.x);
        const offsetY = -this._formatNumber(point.y);

        this.renderer.setAttribute(rayToDraw.rayElement.nativeElement, 'style', `--offsetX: ${offsetX}px; --offsetY: ${offsetY}px;`);   // It is the most performance vay to define styles.
        // this.renderer.setStyle(firstVector, 'font-size', '12px');    // Prevents unsafe styles
    }
}
