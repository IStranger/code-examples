import {ElementRef} from '@angular/core';

import {AnimationPlayerOptionsModel, PointModel} from '../@base';
import {ISvgAnimationPlayer}                     from '../interfaces/svg-animation-player.interface';
import {RayToDrawModel}                          from '../models/ray-to-draw.model';

/**
 * The class describes abstract player for all SVG players.
 */
export abstract class AbstractSvgAnimationPlayer implements ISvgAnimationPlayer {

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
    touchPoint: PointModel;

    /**
     * List of rayToDraw models.
     */
    raysToDraw: RayToDrawModel[];

    /**
     * Starts animation.
     */
    abstract startAnimation();

    /**
     * Set touch point.
     *
     * @param screenPoint
     */
    abstract setTouchPoint(screenPoint: PointModel);

    /**
     * Protected constructor. It may be called only from child constructor.
     */
    protected constructor() {

    }

    /**
     * Formats number to string format with 2 places to the right of the decimal point.
     * @param num
     * @private
     */
    protected _formatNumber(num: number): string {

        return num.toFixed(2);
    }
}
