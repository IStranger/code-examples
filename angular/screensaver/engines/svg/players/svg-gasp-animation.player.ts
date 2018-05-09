import {TweenLite, TweenMax, TimelineLite}   from 'gsap';
import {Linear, Animation as TweenAnimation} from 'gsap';
import {PointModel, RayPositionModel}        from '../@base';
import {RayToDrawModel}                      from '../models/ray-to-draw.model';
import {AbstractSvgAnimationPlayer}          from './abstract-svg-animation.player';

/**
 * The player uses animation based on GreenSock library.
 *
 * @see https://greensock.com/docs/TweenLite
 * @see https://greensock.com/svg-tips
 */
export class SvgGaspAnimationPlayer extends AbstractSvgAnimationPlayer {

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

        // const firstElement = this._getVectorElements(viewportElementRef)[0];
        // TweenLite.to(firstElement, 30, {x: 200, y: 150, repeat: -1, ease: Linear.easeNone});

        this._preparePlayers()
            .forEach((player: TweenAnimation) => player.play());
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
     * Prepares GreenSock animation players.
     */
    protected _preparePlayers(): TweenAnimation[] {

        return this.raysToDraw.map((rayToDraw: RayToDrawModel) => {

            const rayElement    = rayToDraw.rayElement.nativeElement,
                  keyPoints     = this._prepareBezierKeyPoints(rayToDraw.ray.initialPosition),
                  startKeyPoint = keyPoints[0];

            return TweenMax.fromTo(rayElement, 120, startKeyPoint, {
                bezier: {
                    curviness: 1.5,
                    values   : keyPoints,
                },
                ease  : Linear.easeNone,
                repeat: -1,
                // lazy: true,
                paused: true,
            });
        });
    }

    /**
     * Prepares animation key points.
     *
     * @param initPosition Ray initial position.
     */
    protected _prepareBezierKeyPoints(initPosition: RayPositionModel) {

        const rotationAngle = 2 * Math.PI,
              stepMax       = 4;

        const points = [];
        for (let i = 0; i <= stepMax; i++) {

            const progress = i / stepMax,
                  alpha    = initPosition.alpha + rotationAngle * progress,
                  offsetX  = initPosition.radius * Math.cos(alpha),
                  offsetY  = initPosition.radius * Math.sin(alpha);

            points.push({
                x: offsetX,
                y: offsetY
            });
        }

        return points;
    }

}
