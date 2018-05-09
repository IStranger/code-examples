import {Inject, Injectable} from '@angular/core';

import {CONFIG_TOKEN_SCREENSAVER_SERVICE} from '../../di-config/main-service/screensaver-service-config.token';
import {IScreensaverServiceConfig}        from '../../di-config/main-service/screensaver-service-config.interface';
import {IScreensaverConfigValues}         from '../../di-config/main-service/screensaver-service-config-values.interface';
import {IEasingFunc}                      from '../interfaces/easing-func.interface';
import {IGlowDegreeFunc}                  from '../interfaces/glow-degree-func.interface';
import {IPositionProgressFunc}            from '../interfaces/position-progress-func.interface';
import {IncorrectMethodParamsException}   from '../exceptions/incorrect-method-params.exception';
import {AnimationPlayerOptionsModel}      from '../models/animation-player-options.model';
import {PointModel}                       from '../models/point.model';
import {RayOptionsModel}                  from '../models/ray-options.model';
import {RayOrientationModel}              from '../models/ray-orientation.model';
import {RayPositionModel}                 from '../models/ray-position.model';
import {ScreenOptionsModel}               from '../models/screen-options.model';
import {TEngine}                          from '../types/engine.type';
import {TRay}                             from '../types/ray.type';
import {CoordinateService}                from './coordinate.service';

/**
 * The service describes main animation behavior (motion paths etc).
 * Contains methods to prepare animation strategies.
 */
@Injectable()
export class ScreensaverService {

    constructor(@Inject(CONFIG_TOKEN_SCREENSAVER_SERVICE) protected serviceConfig: IScreensaverServiceConfig,
                protected coordinateService: CoordinateService) {
    }

    /**
     * Getter: Returns service config values.
     * If some value is not passed via config, it will be defined by default ("Convention over configuration").
     */
    get config(): IScreensaverConfigValues {
        return {
            engine               : this._getFirstNonNull(this.serviceConfig.engine, <TEngine>'canvas-request-frame'),
            rayOrientationAngle  : this._getFirstNonNull(this.serviceConfig.rayOrientationAngle, 1.11),
            rayQty               : this._getFirstNonNull(this.serviceConfig.rayQty, 100),
            rayGlowDistance      : this._getFirstNonNull(this.serviceConfig.rayGlowDistance, 150),
            rayGlowEasingVelocity: this._getFirstNonNull(this.serviceConfig.rayGlowEasingVelocity, 0.5),
            clockwiseAnimation   : this._getFirstNonNull(this.serviceConfig.clockwiseAnimation, true),
        };
    }

    /**
     * Prepares animation player options.
     */
    prepareAnimationPlayerOptions(): AnimationPlayerOptionsModel {

        const options = new AnimationPlayerOptionsModel();

        options.maxViewportSize      = this._getMaxViewportSize();
        options.screenOptions        = this._prepareScreenOptions(options.maxViewportSize);
        options.rays                 = this._prepareRays(options.maxViewportSize);
        options.progressFunc         = this._prepareProgressFunc(this.config.clockwiseAnimation);
        options.glowDegreeFunc       = this._prepareGlowDegreeFunc();
        options.glowDegreeEasingFunc = this._prepareGlowDegreeEasingFunc(this.config.rayGlowEasingVelocity);

        return options;
    }

    /**
     * Prepares screen options.
     * @private
     */
    protected _prepareScreenOptions(maxViewportSize): ScreenOptionsModel {

        const centerCoordinate = Math.round(maxViewportSize / 2),
              screenOptions    = new ScreenOptionsModel();

        screenOptions.width                       = maxViewportSize;
        screenOptions.height                      = maxViewportSize;
        screenOptions.depth                       = 0;
        screenOptions.worldCoordinateSystemCenter = new PointModel(centerCoordinate, centerCoordinate, 0);

        return screenOptions;
    }

    /**
     * Returns max viewport size (width and height).
     * @private
     */
    protected _getMaxViewportSize(): number {

        // Adaptive canvas size for the mobile displays (may be used to improve performance)

        const displayMaxSize = Math.max(window.innerHeight, window.innerWidth);

        return Math.min(2560, displayMaxSize);
    }

    /**
     * Prepares ray options models.
     * @private
     */
    protected _prepareRays(maxCanvasSize: number): RayOptionsModel[] {

        const rays: RayOptionsModel[] = [];

        // Init ray orientation (polar vector)
        const rayOrientation  = new RayOrientationModel();
        rayOrientation.alpha  = this.config.rayOrientationAngle;
        rayOrientation.radius = Math.round(1.5 * maxCanvasSize);    // More than diagonal


        // Init ray options models (polar vectors)
        for (let vectorNum = 0; vectorNum < this.config.rayQty; vectorNum++) {

            const rayOptions                  = new RayOptionsModel();
            rayOptions.initialPosition        = new RayPositionModel();
            rayOptions.initialPosition.alpha  = 2 * Math.PI * Math.random();               // [0, 2Pi]
            rayOptions.initialPosition.radius = Math.round(maxCanvasSize * (0.05 + 0.65 * Math.random()));   // [5%,65%]
            rayOptions.orientation            = rayOrientation;
            rayOptions.type                   = <TRay>(vectorNum % 3);

            rays.push(rayOptions);
        }

        return rays;
    }

    /**
     * Factory: prepares function to calculate current ray position.
     */
    protected _prepareProgressFunc(clockwise: boolean): IPositionProgressFunc {

        const sign = clockwise ? -1 : 1;

        return (initialPosition: RayPositionModel, progress: number): RayPositionModel => {

            const position = new RayPositionModel();

            position.alpha  = initialPosition.alpha + sign * 2 * Math.PI * progress;
            position.radius = initialPosition.radius;

            return position;
        };
    }

    /**
     * Factory: prepares function to calculate ray glow degree [0,1].
     */
    protected _prepareGlowDegreeFunc(): IGlowDegreeFunc {

        return (ray: RayOptionsModel, touchPoint: PointModel, position: RayPositionModel) => {

            const glowDistance = this.config.rayGlowDistance;
            const distance     = this.coordinateService.calculateDistanceToRay(touchPoint, ray, position);

            if (distance <= glowDistance) {
                return (glowDistance - distance) / glowDistance;    // relative glow [0,1]
            }

            return 0;
        };
    }

    /**
     * Factory: prepares glow degree easing function (to change the value smoothly).
     *
     * @param easingVelocity Relative velocity of change [0;1]
     * @private
     */
    protected _prepareGlowDegreeEasingFunc(easingVelocity: number): IEasingFunc {

        return (currentValue: number, targetValue: number): number => {

            // Check params
            const isNotNumbers = !(Number.isFinite(currentValue) && Number.isFinite(targetValue));

            if (isNotNumbers) {
                throw new IncorrectMethodParamsException(`Method "_changeGlowDegreeSmoothly" supports only number params.`);
            }

            // Change params
            const stepQty        = Math.round(5 / easingVelocity),
                  valueDiffLimit = 0.05;

            // Check if the currentValue is almost the same as targetValue.
            const isTooCloseToTarget = (Math.abs(targetValue - currentValue) <= valueDiffLimit);

            if (isTooCloseToTarget) {
                return targetValue;
            }

            // Change smoothly
            const stepDiff = (targetValue - currentValue) / stepQty;

            return currentValue + stepDiff;
        };
    }

    /**
     * Returns first "not null" values (!== undefined && !== null). It is so called "null coalescing" logic.
     *
     * @param args
     * @private
     *
     * @see https://github.com/tc39/proposal-nullish-coalescing
     */
    protected _getFirstNonNull(...args) {

        for (const arg of args) {
            const isNotNullOrUndefined = !((arg === null) || (arg === undefined));

            if (isNotNullOrUndefined) {
                return arg;
            }
        }

        return args.pop();  // Return last arg by default
    }
}
