import {FactoryProvider, Provider} from '@angular/core';

import {CONFIG_TOKEN_SCREENSAVER_SERVICE} from './main-service/screensaver-service-config.token';
import {CONFIG_TOKEN_SCREENSAVER_MODULE}  from './module/screensaver-module-config.token';
import {IScreensaverServiceConfig}        from './main-service/screensaver-service-config.interface';
import {IScreensaverModuleConfig}         from './module/screensaver-module-config.interface';

/**
 * Factory: Prepares provider to forward module config to nested service (via DI).
 * Forwarding: {@link IScreensaverModuleConfig} --> {@link IScreensaverServiceConfig}
 *
 * @return {Provider}
 */
export const forwardConfigToScreensaverService = (): Provider => {

    return <FactoryProvider>{
        provide   : CONFIG_TOKEN_SCREENSAVER_SERVICE,
        useFactory: (moduleConfig: IScreensaverModuleConfig) => {

            const serviceConfig: IScreensaverServiceConfig = {};

            if (moduleConfig && moduleConfig.animation) {
                // Copy service config from module config
                serviceConfig.engine                = moduleConfig.animation.engine;
                serviceConfig.clockwiseAnimation    = moduleConfig.animation.clockwiseAnimation;
                serviceConfig.rayOrientationAngle   = moduleConfig.animation.rayOrientationAngle;
                serviceConfig.rayQty                = moduleConfig.animation.rayQty;
                serviceConfig.rayGlowDistance       = moduleConfig.animation.rayGlowDistance;
                serviceConfig.rayGlowEasingVelocity = moduleConfig.animation.rayGlowEasingVelocity;
            }

            return serviceConfig;
        },
        deps      : [
            CONFIG_TOKEN_SCREENSAVER_MODULE,
        ]
    };
};
