import {Component, OnInit} from '@angular/core';

import {once}                        from 'lodash-decorators';
import {TEngine}                     from '../../types/engine.type';
import {IAnimationPlayer}            from '../../interfaces/animation-player.interface';
import {AnimationPlayerOptionsModel} from '../../models/animation-player-options.model';
import {ScreensaverService}          from '../../services/screensaver.service';

@Component({
    selector   : 'app-screensaver',
    templateUrl: './screensaver.component.html',
    styleUrls  : ['./screensaver.component.scss']
})
export class ScreensaverComponent implements OnInit {

    /**
     * Constructor.
     */
    constructor(protected screensaverService: ScreensaverService) {
    }

    /**
     * A callback method that is invoked immediately after the default change detector has checked the directive's
     * data-bound properties for the first time, and before any of the view or content children have been checked.
     * It is invoked only once when the directive is instantiated.
     */
    ngOnInit() {
    }

    /**
     * Returns animation engine.
     */
    @once()
    protected _getEngine(): TEngine {

        return this.screensaverService.config.engine;
    }

    /**
     * Checks whether the screensaver is based on a canvas.
     */
    @once()
    protected _isCanvasEngine(): boolean {

        return this._getEngine().startsWith('canvas-');
    }

    /**
     * Checks whether the screensaver is based on a SVG.
     */
    @once()
    protected _isSvgEngine(): boolean {

        return this._getEngine().startsWith('svg-');
    }

    /**
     * Returns animation player options.
     * @private
     */
    @once()
    protected _getPlayerOptions(): AnimationPlayerOptionsModel {

        return this.screensaverService.prepareAnimationPlayerOptions();
    }

    /**
     * Handler for "player init" event.
     *
     * @param animationPlayer
     * @private
     */
    protected _onPlayerInit(animationPlayer: IAnimationPlayer) {

        animationPlayer.startAnimation();
    }
}
