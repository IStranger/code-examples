import {TEngine}                   from '@app/feature/modules/page/modules/screensaver/base/types/engine.type';
import {IScreensaverServiceConfig} from './screensaver-service-config.interface';

/**
 * The interface describes {@link ScreensaverService} config values.
 * All properties are required in comparision with {@link IScreensaverServiceConfig}.
 * Values not passed via config should be defined by default ("Convention over configuration").
 */
export interface IScreensaverConfigValues extends IScreensaverServiceConfig {

    /**
     * Screensaver engine type
     */
    engine: TEngine;

    /**
     * Orientation angle.
     */
    rayOrientationAngle: number;

    /**
     * Rays quantity.
     */
    rayQty: number;

    /**
     * Ray glow distance (between ray line and touch point).
     */
    rayGlowDistance: number;

    /**
     * Ray glow easing velocity ( relative value [0,1] ).
     */
    rayGlowEasingVelocity: number;

    /**
     * Clockwise animation
     */
    clockwiseAnimation: boolean;
}
