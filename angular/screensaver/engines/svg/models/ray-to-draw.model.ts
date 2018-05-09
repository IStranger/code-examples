import {ElementRef} from '@angular/core';

import {RayOptionsModel, RayPositionModel} from '../@base';

/**
 * The model describes ray to draw.
 */
export class RayToDrawModel {

    /**
     * Ray options model.
     */
    ray: RayOptionsModel;

    /**
     * Current position.
     */
    position: RayPositionModel;

    /**
     * HTML/SVG element that represents the ray.
     */
    rayElement: ElementRef;
}
