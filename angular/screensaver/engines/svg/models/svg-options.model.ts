import {ElementRef} from '@angular/core';

import {AnimationPlayerOptionsModel, TEngine} from '../@base';
import {RayToDrawModel}                       from './ray-to-draw.model';

/**
 * The class describes common options of SVG animation.
 */
export class SvgOptionsModel {

    /**
     * Animation engine.
     */
    engine: TEngine;

    /**
     * Common options of animation player.
     */
    playerOptions: AnimationPlayerOptionsModel;

    /**
     * List of models tp draw.
     */
    raysToDraw: RayToDrawModel[];

    /**
     * The element to display animation.
     */
    viewportElement: ElementRef;
}
