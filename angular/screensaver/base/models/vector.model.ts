/**
 * The model describes vector in polar coordinate system.
 */
export class VectorModel {

    /**
     * Rotation radius.
     */
    radius: number;

    /**
     * Rotation angle.
     */
    alpha: number;

    /**
     * Copies data properties from source object.
     * @param sourceVector
     */
    copyFrom(sourceVector: VectorModel): this {

        Object.assign(this, sourceVector);      // NOTE: it may copy symbols
        // this.alpha  = sourceVector.alpha;
        // this.radius = sourceVector.radius;

        return this;
    }
}
