import {Component, ElementRef, Input, OnInit} from '@angular/core';

import {once}                                              from 'lodash-decorators';
import {CoordinateService, PointModel, ScreenOptionsModel} from '../../@base';
import {RayToDrawModel}                                    from '../../models/ray-to-draw.model';

@Component({
    selector   : 'app-svg-ray',
    templateUrl: './svg-ray.component.html',
    styleUrls  : ['./svg-ray.component.css']
})
export class SvgRayComponent implements OnInit {

    /**
     * Ray to draw.
     */
    @Input()
    rayToDraw: RayToDrawModel;

    /**
     * Screen options.
     */
    @Input()
    screenOptions: ScreenOptionsModel;

    /**
     * SVG line position relative to SVG container.
     */
    protected _svgLinePosition = new PointModel(0, 1000, 0);

    /**
     * Constructor.
     */
    constructor(protected coordinateService: CoordinateService,
                protected componentElementRef: ElementRef) {
    }

    /**
     * A callback method that is invoked immediately after the  default change detector has checked the directive's
     * data-bound properties for the first time, and before any of the view or content children have been checked.
     * It is invoked only once when the directive is instantiated.
     */
    ngOnInit(): void {

        this.rayToDraw.rayElement = this.componentElementRef;
    }

    /**
     * Calculates orientation angle in screen coordinate system.
     * @private
     */
    @once()
    protected _getScreenLineAngleXY(): number {

        return this.coordinateService.calculateScreenAngleXY(this.screenOptions, this.rayToDraw.ray.orientation.alpha);
    }
}
