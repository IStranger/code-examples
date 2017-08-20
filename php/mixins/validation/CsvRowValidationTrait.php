<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\mixins\validation;

use common\csv\models\CsvFileMetaData;
use common\csv\helpers\CsvReaderHelper;
use common\csv\exceptions\CsvInvalidParamException;

/**
 * The trait implements {@link IValidatable} and provides validation methods for the {@link CsvRow} model.
 */
trait CsvRowValidationTrait
{
    use CsvBaseValidationTrait;

    /**
     * Checks properties of current object.
     *
     * @throws CsvInvalidParamException
     */
    public function checkProperties(): void
    {
        $this->_checkFileMetaData();
        $this->_checkValues();
        $this->_checkLineNumber();
        $this->_checkParsingErrors();
    }

    /**
     * Checks {@link csvFileMetaData}.
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkFileMetaData(): void
    {
        $isCorrect = $this->csvFileMetaData && ($this->csvFileMetaData instanceof CsvFileMetaData);

        if (!$isCorrect) {
            throw new CsvInvalidParamException('Incorrect "csvFileMetaData" property: It should be instantiated from CsvFileMetaData class.');
        }
    }

    /**
     * Checks {@link values}.
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkValues(): void
    {
        $isCorrect = ($this->values === null) || is_array($this->values);

        if (!$isCorrect) {
            throw new CsvInvalidParamException(CsvReaderHelper::replacePlaceholders('Incorrect "values" property: It should be defined as scalar array. Given value: "{value}".', ['value' => var_export($this->values, true)]));
        }
    }

    /**
     * Checks {@link lineNumber}.
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkLineNumber(): void
    {
        $isCorrect = is_int($this->lineNumber) && ($this->lineNumber > 0);

        if (!$isCorrect) {
            throw new CsvInvalidParamException('Incorrect "lineNumber" property: It should be defined as positive integer value (numeration starts from 1).');
        }
    }

    /**
     * Checks {@link parsingErrors}.
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkParsingErrors(): void
    {
        $isCorrect = is_array($this->parsingErrors);

        if (!$isCorrect) {
            throw new CsvInvalidParamException('Incorrect "parsingErrors" property: It should be defined as scalar array of strings.');
        }
    }
}