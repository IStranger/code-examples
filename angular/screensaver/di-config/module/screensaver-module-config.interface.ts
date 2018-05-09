import {IScreensaverServiceConfig} from '../main-service/screensaver-service-config.interface';

/**
 * The interface describes {@link ScreensaverModule} config.
 */
export interface IScreensaverModuleConfig {
    /**
     * Animation config.
     */
    animation?: IScreensaverServiceConfig;
}
