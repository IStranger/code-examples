import {Component, Input, OnChanges, OnInit, SimpleChanges} from '@angular/core';

import {PointModel} from '../../../../base/models/point.model';


@Component({
    selector   : '[app-svg-line]',
    templateUrl: './svg-line.component.html',
    styleUrls  : ['./svg-line.component.scss']
})
export class SvgLineComponent implements OnInit, OnChanges {

    @Input() start: PointModel;
    @Input() length: number;
    @Input() angleXY: number;
    @Input() typeNum: number;

    _end: PointModel;

    constructor() {
    }

    ngOnInit() {
    }

    ngOnChanges(changes: SimpleChanges): void {

        this._updateEndPoint();
    }

    /**
     * Updates coordinates of end of vector.
     * @private
     */
    protected _updateEndPoint() {

        this._end = this._calculateEndPoint();
    }

    /**
     * Calculates coordinates of end of vector.
     * @private
     */
    protected _calculateEndPoint(): PointModel {

        return new PointModel(
            Math.round(this.start.x + this.length * Math.cos(this.angleXY)),
            Math.round(this.start.y + this.length * Math.sin(this.angleXY)),
            0
        );
    }
}
