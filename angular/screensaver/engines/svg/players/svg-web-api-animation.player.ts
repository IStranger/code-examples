import {PointModel, RayPositionModel} from '../@base';
import {RayToDrawModel}               from '../models/ray-to-draw.model';
import {AbstractSvgAnimationPlayer}   from './abstract-svg-animation.player';


/**
 * The player uses animation based on Animation Web API.
 */
export class SvgWebApiAnimationPlayer extends AbstractSvgAnimationPlayer {

    /**
     * Constructor.
     */
    constructor() {

        super();
    }

    /**
     * Starts animation.
     */
    startAnimation() {

        this._preparePlayers()
            .forEach((player: Animation) => player.play());
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
     * Prepares WebApi Animation players.
     */
    protected _preparePlayers(): Animation[] {

        return this.raysToDraw.map((rayToDraw: RayToDrawModel) => {

            const keyframes  = this._prepareKeyframes(rayToDraw.ray.initialPosition),
                  rayElement = rayToDraw.rayElement.nativeElement;

            return rayElement.animate(keyframes, {
                duration  : 120 * 1000,  // ms
                easing    : 'linear',
                delay     : 0,           // ms
                iterations: Infinity,    // or number
                direction : 'normal',    // 'normal', 'reverse', 'alternate'
                fill      : 'auto'       // 'forwards', 'backwards', 'both', 'none', 'auto'
            });

        });
    }

    /**
     * Prepares animation keyframes.
     *
     * @param initPosition  Ray initial position.
     */
    protected _prepareKeyframes(initPosition: RayPositionModel) {

        const rotationAngle = 2 * Math.PI,
              stepMax       = 60;

        const frames = [];
        for (let i = 0; i <= stepMax; i++) {

            const progress = i / stepMax,
                  alpha    = initPosition.alpha + rotationAngle * progress,
                  offsetX  = this._formatNumber(initPosition.radius * Math.cos(alpha)),
                  offsetY  = this._formatNumber(initPosition.radius * Math.sin(alpha));

            frames.push({
                transform: `translate(${offsetX}px, ${offsetY}px)`,
                offset   : progress
            });
        }

        return frames;
    }

}
