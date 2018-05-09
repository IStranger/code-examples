import {TEngine} from '../../base/types/engine.type';

/**
 * The interface describes {@link ScreensaverService} config.
 * All properties are optional. It is useful
 */
export interface IScreensaverServiceConfig {

    /**
     * Screensaver engine.
     */
    engine?: TEngine;

    /**
     * Orientation angle.
     */
    rayOrientationAngle?: number;

    /**
     * Rays quantity.
     */
    rayQty?: number;

    /**
     * Ray glow distance (between ray line and touch point).
     */
    rayGlowDistance?: number;

    /**
     * Ray glow easing velocity ( relative value [0,1] ).
     */
    rayGlowEasingVelocity?: number;

    /**
     * Clockwise animation
     */
    clockwiseAnimation?: boolean;
}
