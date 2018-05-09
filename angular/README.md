# Angular ScreenSaver Module

The module provides main animation for the my personal site https://fx4.ru.

<table width="100%" border="0" bordercolor="transparent">
<tr>
<td width="80%">

## Key features

 - Configuration via Angular's DI container (see [di-config](screensaver/di-config)).
 - Main animation is implemented using 5 different approaches (see [engines](screensaver/engines)):
    + Canvas + requestAnimationFrame (main engine: [canvas](screensaver/engines/canvas)),
    + SVG + requestAnimationFrame,
    + SVG + Web Animations API,
    + SVG + Css @keyframes,
    + SVG + GreenSock (GASP).
 - Main logic works independently of the animation engine (see [base](screensaver/base)) and
   uses simplified strategy pattern to define general behavior.
 - Main logic uses ["world" coordinate system](https://www.quora.com/What-does-it-mean-by-world-and-world-coordinate-in-computer-graphics).
   The position and orientation of the rays are defined in the polar coordinate system.
   "Screen" points are calculated only in the animation engine.



## Demo

The module works on the https://fx4.ru (canvas based engine).

</td>
<td>
    <p align="right">
         <a href="https://fx4.ru/" target="_blank" title="Go to my homepage">
             <img src="https://github.com/IStranger/code-examples/blob/master/_assets/canvas-animation.gif?raw=true" width="407" alt="Canvas Animation" />
         </a>
    </p>
</td>
</tr>
</table>




## Usage

### Simple use case

The module can be configured at import:

```typescript
import {NgModule} from '@angular/core';

import {ScreensaverModule} from '@shared/screensaver';


@NgModule({
    imports  : [
        ScreensaverModule.forRoot({
            animation: {
                rayQty               : 150,
                clockwiseAnimation   : true,
                // rayOrientationAngle: 1 / 3 * Math.PI,
                // rayGlowDistance   : 200,
                rayGlowEasingVelocity: 0.35,
            }
        }),
    ],
})
export class MyModule {
}

```


### Advanced use case

The module can be configured via DI container:

```typescript
import {NgModule} from '@angular/core';

import {ScreensaverModule}                from '@shared/screensaver';
import {forwardConfigToScreensaverModule} from '@shared/screensaver';


@NgModule({
    imports  : [
        ScreensaverModule,
    ],
    providers: [
        forwardConfigToScreensaverModule(),
    ],
})
export class MyModule {
}


@NgModule({
    imports  : [
        ScreensaverModule.forRoot({
            animation: {
                rayQty               : 150,
                clockwiseAnimation   : true,
                // rayOrientationAngle: 1 / 3 * Math.PI,
                // rayGlowDistance   : 200,
                rayGlowEasingVelocity: 0.35,
            }
        }),
    ],
})
export class MyModule {
}
```

where `forwardConfigToScreensaverModule`:

```typescript
import {FactoryProvider, Provider} from '@angular/core';

import {CONFIG_TOKEN_SCREENSAVER_MODULE} from '@shared/screensaver';
import {CONFIG_TOKEN_MY_MODULE}          from './di-config/my-module-config.token';
import {IMyModuleConfig}                 from './di-config/my-module-config.interface';

/**
 * Factory: Prepares provider to forward myModule config to nested screensaver module (via DI).
 * Forwarding: {@link IMyModuleConfig} --> {@link IScreensaverModuleConfig}
 *
 * @return {Provider}
 */
export const forwardConfigToScreensaverModule = (): Provider => {

    return <FactoryProvider>{
        provide   : CONFIG_TOKEN_SCREENSAVER_MODULE,
        useFactory: (myModuleConfig: IMyModuleConfig) => {

            if (myModuleConfig && myModuleConfig.screensaver) {
                return myModuleConfig.screensaver;
            }

            return {};
        },
        deps      : [CONFIG_TOKEN_MY_MODULE]
    };
};

```