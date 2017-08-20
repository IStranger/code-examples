<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\interfaces;

/**
 * Interface ICsvReader
 */
interface ICsvReader extends \IteratorAggregate
{
    /**
     * Returns row iterator. Each iterated value should implement {@link ICsvRow}.
     *
     * @return \Iterator|\common\csv\interfaces\ICsvRow[]
     */
    public function getIterator(): iterable;
}