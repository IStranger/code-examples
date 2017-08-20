<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\mixins\validation;

use common\csv\helpers\CsvReaderHelper;
use common\csv\exceptions\CsvInvalidParamException;

/**
 * The trait implements {@link IValidatable} and provides validation methods for the {@link CsvFileMetaData} model.
 */
trait CsvMetaDataValidationTrait
{
    use CsvBaseValidationTrait;

    /**
     * Checks properties of current object.
     *
     * @throws CsvInvalidParamException
     */
    public function checkProperties(): void
    {
        $this->_checkHandle();
        $this->_checkHeaders();
        $this->_checkFileSize();
    }

    /**
     * Checks {@link handle}.
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkHandle(): void
    {
        $isCorrect = $this->handle && is_resource($this->handle);

        if (!$isCorrect) {
            throw new CsvInvalidParamException('Incorrect "handle" property: should contain resource handle.');
        }
    }


    /**
     * Checks {@link headers}.
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkHeaders(): void
    {
        $isCorrectArray = is_array($this->headers) && CsvReaderHelper::isScalarArray($this->headers);

        if (!$isCorrectArray) {
            throw new CsvInvalidParamException('Incorrect "headers" property: should contain one-dimensional (scalar) array of strings.');
        }


        // Check headers
        $errorMsg = [];

        // Check for empty header
        $isEmptyHeader = empty($this->headers) || ($this->headers === ['']);
        if ($isEmptyHeader) {
            $errorMsg[] = 'At least one header must be defined.';
        }

        // Check for duplicates
        foreach (array_count_values($this->headers) as $headerName => $duplicatesNumber) {

            if ($duplicatesNumber > 1) {
                $errorMsg[] = CsvReaderHelper::replacePlaceholders('"{header}" duplicates {times} times', ['header' => $headerName, 'times' => $duplicatesNumber]);
            }
        }

        // Add validation errors to model
        if (count($errorMsg) > 0) {

            $msg = CsvReaderHelper::replacePlaceholders('Incorrect "headers": {duplicates}.', ['duplicates' => join(', ', $errorMsg)]);
            throw new CsvInvalidParamException($msg);
        }
    }

    /**
     * Checks {@link fileSize}.
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkFileSize(): void
    {
        $isCorrect = is_null($this->fileSize) || is_int($this->fileSize);   // optional property (int|null)

        if (!$isCorrect) {
            throw new CsvInvalidParamException('Incorrect "fileSize" property: should contain integer value.');
        }
    }
}