import {RayPositionModel} from '../models/ray-position.model';

/**
 * The interface describes function to calculate current position.
 */
export interface IPositionProgressFunc {
    (initialPosition: RayPositionModel, progress: number): RayPositionModel;
}
