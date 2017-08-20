<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\interfaces;

/**
 * The interface describes validatable model.
 */
interface IValidatable
{
    /**
     * Checks properties of current object.
     *
     * @throws \common\csv\exceptions\ICsvReaderException In case of any errors
     */
    public function checkProperties(): void;
}