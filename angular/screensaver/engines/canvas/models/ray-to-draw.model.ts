import {RayPositionModel, RayOptionsModel} from '../@base';

/**
 * The model describes ray to draw.
 */
export class RayToDrawModel {

    /**
     * Permanent ray options.
     */
    ray: RayOptionsModel;

    /**
     * Current ray position (to draw).
     */
    position: RayPositionModel;

    /**
     * Target ray glow degree (relative value [0,1]).
     */
    targetGlowDegree: number;

    /**
     * Current ray glow degree (relative value [0,1]).
     * The value is changed smoothly to {@link targetGlowDegree}.
     */
    currentGlowDegree: number;
}
