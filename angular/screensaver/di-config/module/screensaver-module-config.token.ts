import {InjectionToken} from '@angular/core';

import {IScreensaverModuleConfig} from './screensaver-module-config.interface';

/**
 * The token to inject {@link ScreensaverModule} config.
 *
 * @type {InjectionToken<IScreensaverModuleConfig>}
 */
export const CONFIG_TOKEN_SCREENSAVER_MODULE = new InjectionToken<IScreensaverModuleConfig>('Config: ScreensaverModule');
