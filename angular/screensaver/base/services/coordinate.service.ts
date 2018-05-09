import {Injectable}  from '@angular/core';
import {VectorModel} from '@app/feature/modules/page/modules/screensaver/base';

import {RayPositionModel}   from '../models/ray-position.model';
import {RayOptionsModel}    from '../models/ray-options.model';
import {ScreenOptionsModel} from '../models/screen-options.model';
import {PointModel}         from '../models/point.model';

@Injectable()
export class CoordinateService {

    constructor() {
    }

    /**
     * Calculates SCREEN point for specified point of WORLD coordinate system.
     *
     * @param screenOptions
     * @param worldPoint
     */
    calculateScreenPoint(screenOptions: ScreenOptionsModel, worldPoint: PointModel): PointModel {

        return new PointModel(
            screenOptions.worldCoordinateSystemCenter.x + worldPoint.x,
            screenOptions.worldCoordinateSystemCenter.y - worldPoint.y,
            screenOptions.worldCoordinateSystemCenter.z + worldPoint.z,
        );
    }

    /**
     * Calculates WORLD point for specified point of SCREEN coordinate system.
     *
     * @param screenOptions
     * @param screenPoint
     */
    calculateWorldPoint(screenOptions: ScreenOptionsModel, screenPoint: PointModel): PointModel {

        return new PointModel(
            screenPoint.x - screenOptions.worldCoordinateSystemCenter.x,
            -screenPoint.y + screenOptions.worldCoordinateSystemCenter.y,
            screenPoint.z - screenOptions.worldCoordinateSystemCenter.z,
        );
    }

    /**
     * Calculates Cartesian point for the specified point in polar coordinate system.
     */
    calculateCartesianPointFromPolar(polarPoint: VectorModel): PointModel {

        const startPoint = new PointModel(0, 0, 0);

        return this.calculateVectorEndPoint(startPoint, polarPoint);
    }

    /**
     * Calculates vector end point.
     *
     * @param startPoint
     * @param vector
     */
    calculateVectorEndPoint(startPoint: PointModel, vector: VectorModel): PointModel {

        return new PointModel(
            startPoint.x + vector.radius * Math.cos(vector.alpha),
            startPoint.y + vector.radius * Math.sin(vector.alpha),
            startPoint.z + 0
        );
    }


    /**
     * Calculates SCREEN angle (in XY plane) for specified angle of WORLD coordinate system.
     *
     * @param screenOptions
     * @param worldAngleXY
     */
    calculateScreenAngleXY(screenOptions: ScreenOptionsModel, worldAngleXY: number): number {

        return -worldAngleXY;
    }

    /**
     * Calculates distance between point and ray.
     *
     * @param point Point in the WORLD coordinate system.
     * @param ray
     * @param rayPosition
     */
    calculateDistanceToRay(point: PointModel, ray: RayOptionsModel, rayPosition: RayPositionModel): number {

        /*
         * Ray with position (r;alpha) in polar coordinate system and permanent orientation "beta" may be defined by equation:
         *
         *      y = k*x + b, where:
         *          k = tan(beta)
         *          b = r*(sin(alpha) - tan(beta)*cos(alpha))
         *
         * OR in canonical form:
         *
         *      a*x + b*x + c = 0, where:
         *          a = tan(beta)
         *          b = -1
         *          c = r*(sin(alpha) - tan(beta)*cos(alpha))
         *
         * The distance between point (Xp,Yp) and line (a*x + b*x + c = 0) may be calculated by expression:
         *
         *      distance = |a*Xp + b*Yp + c| / sqrt( a^2 + b^2 )
         */

        const a = Math.tan(ray.orientation.alpha),
              b = -1,
              c = rayPosition.radius * (Math.sin(rayPosition.alpha) - a * Math.cos(rayPosition.alpha));

        return Math.abs(a * point.x + b * point.y + c) / Math.sqrt(a * a + b * b);
    }
}
