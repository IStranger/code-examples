<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\components;

/**
 * Class AbstractCsvRow
 */
abstract class AbstractCsvRow implements \common\csv\interfaces\ICsvRow
{
    /**
     * Checks if row contain any errors.
     * For example: line cannot be parsed, or it contains unexpected number of cells.
     * Error descriptions can be retrieved via {@link getErrors()}.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return count($this->getErrors()) > 0;
    }
}