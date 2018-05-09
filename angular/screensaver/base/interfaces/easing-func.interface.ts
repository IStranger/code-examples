/**
 * The interface describes easing function to change value currentValue --> targetValue.
 * Example:
 *      ```
 *          let currentValue = 0;
 *          let targetValue  = 10;
 *          for (...) {
 *              currentValue = easingFunc(currentValue, targetValue);   // After several iterations `currentValue` will be equal `targetValue`.
 *              // ...
 *          }
 *      ```
 */
export interface IEasingFunc {
    (currentValue: number, targetValue: number): number;
}
