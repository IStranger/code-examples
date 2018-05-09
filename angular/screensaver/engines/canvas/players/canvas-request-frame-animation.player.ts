import {ElementRef} from '@angular/core';

import {AnimateService, TDrawFunction}                            from '@app/shared/modules/animate';
import {AnimationPlayerOptionsModel, RayOptionsModel, PointModel} from '../@base';
import {IAnimationPlayer}                                         from '../@base';
import {CoordinateService}                                        from '../@base';
import {CanvasOptionsModel}                                       from '../models/canvas-options.model';
import {RayToDrawModel}                                           from '../models/ray-to-draw.model';

/**
 * The class implements screensaver animation based on Animation Web API and Canvas.
 */
export class CanvasRequestFrameAnimationPlayer implements IAnimationPlayer {

    /**
     * Common options of the animation player.
     */
    options: AnimationPlayerOptionsModel;

    /**
     * An element to display animation.
     */
    viewportElement: ElementRef;

    /**
     * The mouse pointer position.
     */
    touchPoint: PointModel = null;

    /**
     * Constructor.
     */
    constructor(protected coordinateService: CoordinateService,
                protected animateService: AnimateService) {
    }

    /**
     * Starts animation.
     */
    startAnimation() {

        const canvasOptions           = new CanvasOptionsModel();
        canvasOptions.viewportElement = this.viewportElement.nativeElement;
        canvasOptions.context         = this._getContext(canvasOptions.viewportElement);
        canvasOptions.screenOptions   = this.options.screenOptions;

        // this._drawRay(canvasOptions, rays[0], rays[0].initPosition);     // For debug

        this.animateService.animate({
            duration         : 120 * 1000,
            infinite         : true,
            runOutsideAngular: true,
            timing           : this.animateService.getTimingFuncByAlias('ease-in-linear'),
            draw             : this._prepareDrawFunc(canvasOptions, this.options.rays),
        });
    }

    /**
     * Sets touch point.
     *
     * @param screenPoint
     */
    setTouchPoint(screenPoint: PointModel) {

        this.touchPoint = this.coordinateService.calculateWorldPoint(this.options.screenOptions, screenPoint);
    }

    /**
     * Prepares draw function. progress - relative progress [0,1].
     * @private
     */
    protected _prepareDrawFunc(canvasOptions: CanvasOptionsModel, rays: RayOptionsModel[]): TDrawFunction {

        const raysToDraw = this._initRayToDrawModels(rays);

        return (progress: number): void => {

            this._clearViewport(canvasOptions);

            for (const rayToDraw of raysToDraw) {

                rayToDraw.position = this.options.progressFunc(rayToDraw.ray.initialPosition, progress);

                if (this.touchPoint) {
                    rayToDraw.targetGlowDegree  = this.options.glowDegreeFunc(rayToDraw.ray, this.touchPoint, rayToDraw.position);
                    rayToDraw.currentGlowDegree = this.options.glowDegreeEasingFunc(rayToDraw.currentGlowDegree, rayToDraw.targetGlowDegree);
                }

                this._drawRay(canvasOptions, rayToDraw);
            }
        };
    }

    /**
     * Initializes ray to draw models.
     *
     * @param rays
     */
    protected _initRayToDrawModels(rays: RayOptionsModel[]): RayToDrawModel[] {

        return rays.map((ray) => {

            const rayToDraw = new RayToDrawModel();

            rayToDraw.ray               = ray;
            rayToDraw.position          = ray.initialPosition;
            rayToDraw.targetGlowDegree  = 0;
            rayToDraw.currentGlowDegree = 0;

            return rayToDraw;
        });
    }

    /**
     * Draws ray in current position.
     *
     * @param canvasOptions Target canvas options.
     * @param drawRay    Ray options
     * @private
     */
    protected _drawRay(canvasOptions: CanvasOptionsModel, drawRay: RayToDrawModel) {

        const colorMap = [
            '#f93f89',
            '#04cdd4',
            '#2e55bf',
        ];

        const from       = this.coordinateService.calculateCartesianPointFromPolar(drawRay.position);
        const to         = this.coordinateService.calculateVectorEndPoint(from, drawRay.ray.orientation);
        const screenFrom = this.coordinateService.calculateScreenPoint(canvasOptions.screenOptions, from);
        const screenTo   = this.coordinateService.calculateScreenPoint(canvasOptions.screenOptions, to);
        const lineColor  = this._getRayLineColor(drawRay.currentGlowDegree);

        this._drawLine(canvasOptions.context, screenFrom, screenTo, lineColor, colorMap[drawRay.ray.type]);
    }

    /**
     * Returns ray line color depending on glow degree.
     *
     * @param glowDegree
     * @private
     */
    protected _getRayLineColor(glowDegree): string {

        // Base color #6c6d75 or hsl(233,8%,46%)
        const brightness = Math.round(46 + 44 * glowDegree);    // Change brightness between [46%,90%]

        return `hsl(233,8%,${brightness}%)`;
    }

    /**
     * Draws line.
     * @param context
     * @param from
     * @param to
     * @param lineColor
     * @param pointColor
     * @private
     */
    protected _drawLine(context: CanvasRenderingContext2D, from: PointModel, to: PointModel, lineColor: string, pointColor: string) {
        context.beginPath();

        context.moveTo(from.x, from.y);
        context.lineTo(to.x, to.y);

        context.lineWidth   = 0.2;
        context.strokeStyle = lineColor;
        context.stroke();
        context.closePath();

        context.beginPath();
        context.arc(from.x, from.y, 1, 0, 2 * Math.PI);
        context.lineWidth   = 0.3;
        context.strokeStyle = pointColor;
        context.fillStyle   = pointColor;
        context.fill();
    }

    /**
     * Clears context.
     * @private
     * @param canvasOptions
     */
    protected _clearViewport(canvasOptions: CanvasOptionsModel) {

        canvasOptions.context.clearRect(0, 0, canvasOptions.viewportElement.width, canvasOptions.viewportElement.height);
    }

    /**
     * Returns models context.
     * @param viewportElement
     * @private
     */
    protected _getContext(viewportElement: HTMLCanvasElement): CanvasRenderingContext2D {

        return viewportElement.getContext('2d');
    }

}
