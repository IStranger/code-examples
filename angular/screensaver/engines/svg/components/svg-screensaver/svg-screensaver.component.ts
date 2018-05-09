import {AfterViewInit, Component, ElementRef, OnInit} from '@angular/core';
import {Input, Output, ViewChild, EventEmitter}       from '@angular/core';

import {IAnimationPlayer, TEngine}   from '../../@base';
import {AnimationPlayerOptionsModel} from '../../@base';
import {RayToDrawModel}              from '../../models/ray-to-draw.model';
import {SvgOptionsModel}             from '../../models/svg-options.model';
import {ISvgAnimationPlayer}         from '../../interfaces/svg-animation-player.interface';
import {SvgAnimationService}         from '../../services/svg-animation.service';


@Component({
    selector   : 'app-svg-screensaver',
    templateUrl: './svg-screensaver.component.html',
    styleUrls  : ['./svg-screensaver.component.scss'],
    providers  : [
        SvgAnimationService,
    ]
})
export class SvgScreensaverComponent implements OnInit, AfterViewInit {

    /**
     * Animation engine.
     */
    @Input()
    engine: TEngine;

    /**
     * Common options of animation player.
     */
    @Input()
    playerOptions: AnimationPlayerOptionsModel;

    /**
     * Event will be triggered when animation player will be initialized.
     */
    @Output()
    playerInit: EventEmitter<IAnimationPlayer> = new EventEmitter();

    /**
     * The element to display animation.
     */
    @ViewChild('viewport')
    viewportElementRef: ElementRef;

    /**
     * Animation player object.
     */
    protected _animationPlayer: ISvgAnimationPlayer;

    /**
     * List of models to draw.
     */
    protected _raysToDraw: RayToDrawModel[];

    /**
     * Constructor.
     */
    constructor(protected svgAnimationService: SvgAnimationService) {
    }

    /**
     * A callback method that is invoked immediately after the default change detector has checked the directive's
     * data-bound properties for the first time, and before any of the view or content children have been checked.
     * It is invoked only once when the directive is instantiated.
     */
    ngOnInit(): void {

        this._initRaysToDraw();
    }

    /**
     * A callback method that is invoked immediately after Angular has completed initialization of a component's view.
     * It is invoked only once when the view is instantiated.
     */
    ngAfterViewInit(): void {

        this._initComponent();
    }

    /**
     * Initializes rayToDraw models.
     */
    protected _initRaysToDraw() {

        // NOTE: rayToDraw.rayElement will be initialized by child component (after initialization)
        this._raysToDraw = this.svgAnimationService.initRaysToDraw(this.playerOptions);
    }

    /**
     * Initializes component.
     */
    protected _initComponent() {

        // Prepare SVG animation options model
        const options = this._prepareSvgAnimationOptions();

        // Prepare player
        this._animationPlayer = this.svgAnimationService.preparePlayer(options);

        // Trigger event
        this.playerInit.emit(this._animationPlayer);
    }

    /**
     * Prepares SVG animation options.
     */
    protected _prepareSvgAnimationOptions(): SvgOptionsModel {

        const options = new SvgOptionsModel();

        options.engine          = this.engine;
        options.playerOptions   = this.playerOptions;
        options.raysToDraw      = this._raysToDraw;             // each rayToDraw.rayElement must contain correct link to the svgLine element
        options.viewportElement = this.viewportElementRef;

        return options;
    }

}
