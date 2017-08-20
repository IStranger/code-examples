<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\interfaces;

/**
 * Interface ICsvRow describes single parsed CSV row.
 */
interface ICsvRow
{
    /**
     * Returns parsed values. In case of any errors of parsing it may return =null.
     *
     * @return string[]|null
     */
    public function getValues(): ?array;

    /**
     * Prepares row values indexed by CSV headers.
     *
     * @return array In format: ['header1' => 'value1', 'header2' => 'value2'].
     *
     * @throws \common\csv\exceptions\CsvReaderException    If row is incorrect (for example, headers and values have
     *                                                      different number of items).
     */
    public function getValuesIndexedByHeaders(): array;

    /**
     * Returns number of line in source file.
     * Row numerations starts from 1 (first *data* row has number 1).
     *
     * @return int
     */
    public function getLineNumber(): int;

    /**
     * Checks if row contain any errors.
     * For example: line cannot be parsed, or it contains unexpected number of cells.
     * Error descriptions can be retrieved via {@link getErrors()}.
     *
     * @return bool
     */
    public function hasErrors(): bool;

    /**
     * Returns text description of errors.
     * For example: line cannot be parsed, or it contains unexpected number of cells.
     *
     * @return string[] Error list. If row has not any errors, returns empty array.
     */
    public function getErrors(): array;
}