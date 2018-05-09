import {ScreenOptionsModel} from '../@base';

/**
 * The model describes canvas options.
 */
export class CanvasOptionsModel {
    viewportElement: HTMLCanvasElement;
    context: CanvasRenderingContext2D;
    screenOptions: ScreenOptionsModel;
}
