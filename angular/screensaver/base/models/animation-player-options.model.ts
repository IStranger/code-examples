import {IEasingFunc}           from '../interfaces/easing-func.interface';
import {IGlowDegreeFunc}       from '../interfaces/glow-degree-func.interface';
import {IPositionProgressFunc} from '../interfaces/position-progress-func.interface';
import {RayOptionsModel}       from './ray-options.model';
import {ScreenOptionsModel}    from './screen-options.model';


/**
 * The class describes common options of the animation player {@link IAnimationPlayer}.
 */
export class AnimationPlayerOptionsModel {

    /**
     * Max viewport size.
     */
    maxViewportSize: number;

    /**
     * Screen options.
     */
    screenOptions: ScreenOptionsModel;

    /**
     * The function describes evolution of the ray positions.
     */
    progressFunc: IPositionProgressFunc;

    /**
     * The function describes ray glow degree depending on ray position and touch point.
     */
    glowDegreeFunc: IGlowDegreeFunc;

    /**
     * The function describes ray glow degree depending on ray position and touch point.
     */
    glowDegreeEasingFunc: IEasingFunc;

    /**
     * List of rays options.
     */
    rays: RayOptionsModel[];
}
