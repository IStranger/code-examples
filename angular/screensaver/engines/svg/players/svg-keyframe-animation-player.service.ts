import {Renderer2} from '@angular/core';

import {PointModel}                 from '../@base';
import {AbstractSvgAnimationPlayer} from './abstract-svg-animation.player';

/**
 * The player uses animation based on @keyframe (CSS)
 */
export class SvgKeyframeAnimationPlayer extends AbstractSvgAnimationPlayer {

    /**
     * Constructor.
     */
    constructor(protected renderer: Renderer2) {

        super();
    }

    /**
     * Starts animation.
     */
    startAnimation() {

        const keyframes = this._prepareKeyframes();
        this._printAnimationSteps(keyframes);

        this._startRayAnimation();
    }

    /**
     * Set touch point.
     *
     * @param screenPoint
     */
    setTouchPoint(screenPoint: PointModel) {

        // Has no implementation.
    }

    /**
     * Prints keyframes to console.
     */
    protected _printAnimationSteps(keyframes: AnimationKeyframe[]) {

        const lines = [];
        for (const keyframe of keyframes) {

            const line = ` ${this._formatNumber(keyframe.percent)}% { transform: translate(${this._formatNumber(keyframe.x)}px, ${this._formatNumber(keyframe.y)}px); }`;
            lines.push(line);
        }

        console.log(lines.join('\n'));
    }

    /**
     * Prepares keyframes.
     *
     * @param radius
     */
    protected _prepareKeyframes(radius: number = 200): AnimationKeyframe[] {

        const fullAngle = 2 * Math.PI,
              stepMax   = 60,
              keyframes = [];

        for (let i = 0; i <= stepMax; i++) {

            const angle    = fullAngle * i / stepMax,
                  keyframe = new AnimationKeyframe();

            keyframe.percent = i / stepMax * 100;
            keyframe.x       = radius * Math.cos(angle);
            keyframe.y       = radius * Math.sin(angle);

            keyframes.push(keyframe);
        }

        return keyframes;
    }

    /**
     * Starts CSS animation (adds CSS class to all vectors).
     */
    protected _startRayAnimation() {

        for (const rayToDraw of this.raysToDraw) {
            const rayElement = rayToDraw.rayElement.nativeElement;
            this.renderer.addClass(rayElement, 'translate');
        }
    }
}

/**
 * Describes CSS animation keyframe.
 */
class AnimationKeyframe {
    percent: number;
    x: number;
    y: number;
}
