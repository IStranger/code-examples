<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv;

use common\csv\helpers\CsvReaderHelper;
use common\csv\interfaces\IValidatable;
use common\csv\mixins\ConfigurableObjectTrait;
use common\csv\mixins\validation\CsvRowValidationTrait;
use common\csv\exceptions\CsvReaderException;
use common\csv\exceptions\CsvInvalidParamException;

/**
 * The class represents single parsed CSV row.
 */
class CsvRow extends \common\csv\components\AbstractCsvRow implements IValidatable
{
    use ConfigurableObjectTrait;
    use CsvRowValidationTrait;

    /**
     * @var \common\csv\models\CsvFileMetaData Metadata of source CSV file.
     */
    protected $csvFileMetaData;

    /**
     * @var string[]|null Values parsed from string. In case of any errors of parsing it may contain =null.
     */
    protected $values;

    /**
     * @var int Number of line in source CSV file.
     */
    protected $lineNumber;

    /**
     * @var string[] Parsing errors.
     */
    protected $parsingErrors = [];

    /**
     * CsvRow constructor.
     * Configures current object with the specified property values.
     *
     * @param array $config
     *
     * @throws CsvInvalidParamException  If some property is incorrect.
     */
    public function __construct($config = [])
    {
        $this->_initRow($config);
    }

    /**
     * Prepares row values indexed by CSV headers.
     *
     * @return array In format: ['header1' => 'value1', 'header2' => 'value2'].
     *
     * @throws \common\csv\exceptions\CsvReaderException    If row is incorrect (for example, headers and values have
     *                                                      different number of items).
     */
    public function getValuesIndexedByHeaders(): array
    {
        if ($this->hasErrors()) {
            throw new CsvReaderException(CsvReaderHelper::replacePlaceholders('Incorrect row: {data}', ['data' => json_encode($this->values)]));
        }

        return array_combine($this->csvFileMetaData->headers, $this->values);
    }

    /**
     * Returns parsed values. In case of any errors of parsing it may return =null.
     *
     * @return string[]|null
     */
    public function getValues(): ?array
    {
        return $this->values;
    }

    /**
     * Returns number of line in source file.
     * Row numerations starts from 1 (first *data* row has number 1).
     *
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * Returns text description of errors.
     * For example: line cannot be parsed, or it contains unexpected number of cells.
     *
     * @return string[] Error list. If row has not any errors, returns empty array.
     */
    public function getErrors(): array
    {
        $errors = $this->parsingErrors;

        if ($this->values) {

            // Check empty lines
            $isEmptyLine =
                (count($this->values) === 1) &&
                (CsvReaderHelper::getFirst($this->values) === '');

            if ($isEmptyLine) {
                $errors[] = 'Empty line';
            }

            // Check number of values
            $isIncorrectValuesNumber = count($this->csvFileMetaData->headers) !== count($this->values);
            if ($isIncorrectValuesNumber) {
                $errors[] = 'Cells number does not match the number of headers.';
            }

        }

        return $errors;
    }

    /**
     * Initializes row.
     *
     * @param $config
     *
     * @throws CsvInvalidParamException     If some property is incorrect.
     */
    protected function _initRow($config)
    {
        $this->_configureFrom($config);
        $this->checkProperties();
    }
}