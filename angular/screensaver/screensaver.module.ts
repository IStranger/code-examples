import {ModuleWithProviders, NgModule} from '@angular/core';
import {CommonModule}                  from '@angular/common';

import {AnimateService}                         from '@app/shared/modules/animate';
import {SafeNgStyleModule}                      from '@app/shared';
import {forwardConfigToScreensaverService}      from './di-config/forward-config-to-screensaver-service.provider';
import {prepareScreensaverModuleConfigProvider} from './di-config/module/screensaver-module-config.provider';
import {IScreensaverModuleConfig}               from './di-config/module/screensaver-module-config.interface';
import {ScreensaverService}                     from './base/services/screensaver.service';
import {CoordinateService}                      from './base/services/coordinate.service';
import {ScreensaverComponent}                   from './base/components/screensaver/screensaver.component';
import {CanvasScreensaverComponent}             from './engines/canvas/components/canvas-screensaver/canvas-screensaver.component';
import {SvgScreensaverComponent}                from './engines/svg/components/svg-screensaver/svg-screensaver.component';
import {SvgRayComponent}                        from './engines/svg/components/svg-ray/svg-ray.component';
import {SvgLineComponent}                       from './engines/svg/components/svg-line/svg-line.component';

@NgModule({
    declarations: [
        CanvasScreensaverComponent,
        SvgScreensaverComponent,
        SvgLineComponent,
        ScreensaverComponent,
        SvgRayComponent,
    ],
    imports     : [
        CommonModule,
        SafeNgStyleModule,
    ],
    providers   : [
        forwardConfigToScreensaverService(),
        AnimateService,
        ScreensaverService,
        CoordinateService,
    ],
    exports     : [
        ScreensaverComponent
    ]
})
export class ScreensaverModule {

    /**
     * Returns module wrapper (with module config injector).
     *
     * @param {IScreensaverModuleConfig} moduleConfig
     * @return {ModuleWithProviders<ScreensaverModule>}
     */
    static forRoot(moduleConfig: IScreensaverModuleConfig): ModuleWithProviders<ScreensaverModule> {
        return {
            ngModule : ScreensaverModule,
            providers: [
                prepareScreensaverModuleConfigProvider(moduleConfig),
            ]
        };
    }
}
