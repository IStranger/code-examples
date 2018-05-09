import {InjectionToken} from '@angular/core';

import {IScreensaverServiceConfig} from './screensaver-service-config.interface';

/**
 * The token to inject {@link ScreensaverService} config.
 *
 * @type {InjectionToken<IScreensaverServiceConfig>}
 */
export const CONFIG_TOKEN_SCREENSAVER_SERVICE = new InjectionToken<IScreensaverServiceConfig>('Config: ScreensaverService (main service of ScreensaverModule)');
