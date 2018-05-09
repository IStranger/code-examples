import {PointModel}       from '../models/point.model';
import {RayOptionsModel}  from '../models/ray-options.model';
import {RayPositionModel} from '../models/ray-position.model';

/**
 * The interface describes function to calculate relative glow degree [0,1] depending on position and touch point.
 */
export interface IGlowDegreeFunc {
    (ray: RayOptionsModel, touchPoint: PointModel, position: RayPositionModel): number;
}
