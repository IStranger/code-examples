import {Injectable, Renderer2} from '@angular/core';

import {AnimateService}                               from '@app/shared/modules/animate';
import {AnimationPlayerOptionsModel, RayOptionsModel} from '../@base';
import {CoordinateService}                            from '../@base';
import {RayToDrawModel}                               from '../models/ray-to-draw.model';
import {SvgOptionsModel}                              from '../models/svg-options.model';
import {NotSupportedEngineException}                  from '../exceptions/not-supported-engine.exception';
import {ISvgAnimationPlayer}                          from '../interfaces/svg-animation-player.interface';
import {SvgRequestFrameAnimationPlayer}               from '../players/svg-request-frame-animation.player';
import {SvgWebApiAnimationPlayer}                     from '../players/svg-web-api-animation.player';
import {SvgGaspAnimationPlayer}                       from '../players/svg-gasp-animation.player';
import {SvgKeyframeAnimationPlayer}                   from '../players/svg-keyframe-animation-player.service';


/**
 * Svg animation service.
 */
@Injectable()
export class SvgAnimationService {

    constructor(protected renderer: Renderer2,
                protected animateService: AnimateService,
                protected coordinateService: CoordinateService) {
    }

    /**
     * Prepares animation player.
     *
     * @param options
     */
    preparePlayer(options: SvgOptionsModel): ISvgAnimationPlayer {

        const player = this._createAnimationPlayer(options);

        player.viewportElement = options.viewportElement;
        player.options         = options.playerOptions;
        player.raysToDraw      = options.raysToDraw;

        return player;
    }

    /**
     * Initializes rayToDraw models.
     *
     * @param playerOptions
     */
    initRaysToDraw(playerOptions: AnimationPlayerOptionsModel): RayToDrawModel[] {

        return playerOptions.rays.map(this._createRayToDraw);
    }

    /**
     * Factory: creates animation player for the specified animation options.
     *
     * @param options
     */
    protected _createAnimationPlayer(options: SvgOptionsModel): ISvgAnimationPlayer {

        switch (options.engine) {

            case 'svg-request-frame':
                return new SvgRequestFrameAnimationPlayer(this.animateService, this.coordinateService, this.renderer);

            case 'svg-web-api':
                return new SvgWebApiAnimationPlayer();

            case 'svg-gasp':
                return new SvgGaspAnimationPlayer();

            case 'svg-keyframe':
                return new SvgKeyframeAnimationPlayer(this.renderer);

            default:
                throw new NotSupportedEngineException(`Animation engine "${options.engine}" is not supported by SvgAnimationService.`);
        }

    }

    /**
     * Creates RayToDrawModel (init state).
     *
     * @param ray
     */
    protected _createRayToDraw(ray: RayOptionsModel): RayToDrawModel {

        const rayToDraw = new RayToDrawModel();
        rayToDraw.ray   = ray;
        // rayToDraw.position = ray.initialPosition;       // it is not used for initialization

        return rayToDraw;
    }
}
