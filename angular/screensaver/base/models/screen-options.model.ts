import {PointModel} from './point.model';

/**
 * The class describes screen options.
 * It is assumed that world coordinate system has the same size as screen (however it has different starting point).
 */
export class ScreenOptionsModel {

    /**
     * Screen size along the X axis
     */
    width: number;

    /**
     * Screen size along the Y axis
     */
    height: number;

    /**
     * Screen size along the Z axis
     */
    depth: number;

    /**
     * Center of WORLD coordinate system.
     */
    worldCoordinateSystemCenter: PointModel;
}
