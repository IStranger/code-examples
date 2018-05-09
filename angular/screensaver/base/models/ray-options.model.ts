import {TRay}                from '../types/ray.type';
import {RayPositionModel}    from './ray-position.model';
import {RayOrientationModel} from './ray-orientation.model';

/**
 * The model describes ray options.
 */
export class RayOptionsModel {

    /** Initial position */
    initialPosition: RayPositionModel;

    /** Permanent orientation */
    orientation: RayOrientationModel;

    /** Ray type (affects point color) */
    type: TRay;
}
