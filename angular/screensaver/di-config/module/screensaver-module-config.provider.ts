import {Provider, ValueProvider} from '@angular/core';

import {CONFIG_TOKEN_SCREENSAVER_MODULE} from './screensaver-module-config.token';
import {IScreensaverModuleConfig}        from './screensaver-module-config.interface';

/**
 * Prepares provider to inject {@link IScreensaverModuleConfig}.
 *
 * @return {Provider}
 */
export const prepareScreensaverModuleConfigProvider = (moduleConfig: IScreensaverModuleConfig): Provider => {

    return <ValueProvider>{
        provide : CONFIG_TOKEN_SCREENSAVER_MODULE,
        useValue: moduleConfig,
    };
};
