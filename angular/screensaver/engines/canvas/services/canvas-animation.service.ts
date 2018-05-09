import {ElementRef, Injectable} from '@angular/core';

import {AnimateService}                    from '@app/shared/modules/animate';
import {AnimationPlayerOptionsModel}       from '../@base';
import {IAnimationPlayer}                  from '../@base';
import {CoordinateService}                 from '../@base';
import {CanvasRequestFrameAnimationPlayer} from '../players/canvas-request-frame-animation.player';

/**
 * Canvas animation service.
 */
@Injectable()
export class CanvasAnimationService {

    constructor(protected coordinateService: CoordinateService,
                protected animateService: AnimateService) {
    }

    /**
     * Prepares animation player.
     *
     * @param options
     * @param viewportElement
     */
    public preparePlayer(options: AnimationPlayerOptionsModel, viewportElement: ElementRef): IAnimationPlayer {

        const player = new CanvasRequestFrameAnimationPlayer(this.coordinateService, this.animateService);

        player.viewportElement = viewportElement;
        player.options         = options;

        return player;
    }

}
