import {ElementRef} from '@angular/core';

import {AnimationPlayerOptionsModel} from '../models/animation-player-options.model';
import {PointModel}                  from '../models/point.model';

/**
 * Describes abstract animation player.
 */
export interface IAnimationPlayer {

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
     * Starts animation.
     */
    startAnimation();

    /**
     * Set touch point.
     *
     * @param screenPoint
     */
    setTouchPoint(screenPoint: PointModel);
}
