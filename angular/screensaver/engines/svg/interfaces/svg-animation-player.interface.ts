import {RayToDrawModel}   from '@app/feature/modules/page/modules/screensaver/engines/svg/models/ray-to-draw.model';
import {IAnimationPlayer} from '../@base';

/**
 * The interface describes SVG animation player.
 */
export interface ISvgAnimationPlayer extends IAnimationPlayer {

    /**
     * List of rayToDraw models.
     */
    raysToDraw: RayToDrawModel[];
}
