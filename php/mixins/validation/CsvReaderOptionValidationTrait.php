<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\mixins\validation;

use common\csv\CsvRow;
use common\csv\models\CsvFileMetaData;
use common\csv\helpers\CsvReaderHelper;
use common\csv\exceptions\CsvInvalidParamException;

/**
 * The trait implements {@link IValidatable} and provides validation methods for the {@link CsvReaderOptions} model.
 */
trait CsvReaderOptionValidationTrait
{
    use CsvBaseValidationTrait;

    /**
     * Checks properties of current object.
     *
     * @throws CsvInvalidParamException
     */
    public function checkProperties(): void
    {
        $this->_validateModel();
    }

    /**
     * Validates current model and throws exception in case of any errors.
     */
    protected function _validateModel()
    {
        $hasErrors = !$this->validate();

        if ($hasErrors) {
            $errors    = $this->getFirstErrors();
            $errorText = join(', ', $errors);

            throw new CsvInvalidParamException($errorText);
        }
    }

    /**
     * @inherited
     */
    public function rules()
    {
        return [
            [['csvPath', 'skipFirstRow', 'csvRowClass', 'csvFileMetaDataClass'], self::VALIDATOR_REQUIRED],

            ['skipFirstRow', self::VALIDATOR_BOOLEAN],
            ['csvPath', function ($attrName) {
                $this->_validateCsvPath($attrName);
            }],
            ['headers', function ($attrName) {
                $this->_validateHeaders($attrName);
            }],
            ['csvRowClass', function ($attrName) {
                $this->_validateCsvRowClass($attrName);
            }],
            ['csvFileMetaDataClass', function ($attrName) {
                $this->_validateCsvFileMetaDataClass($attrName);
            }],

            [['csvDelimiter', 'csvEscape', 'csvEnclosure'], self::VALIDATOR_STRING, 'min' => 1, 'max' => 1, 'skipOnEmpty' => false],
        ];
    }


    /**
     * Validates CSV path.
     *
     * @param string $attrName
     */
    protected function _validateCsvPath(string $attrName): void
    {
        $csvPath = $this->{$attrName};
        $params  = ['attr' => $attrName, 'path' => $csvPath];

        if (!file_exists($csvPath)) {
            $this->addError($attrName, CsvReaderHelper::replacePlaceholders('Incorrect option "{attr}": File "{path}" does not exist.', $params));
        }

        if (!is_readable($csvPath)) {
            $this->addError($attrName, CsvReaderHelper::replacePlaceholders('Incorrect option "{attr}": File "{path}" is not readable.', $params));
        }
    }

    /**
     * Validates CSV path.
     *
     * @param string $attrName
     */
    protected function _validateHeaders(string $attrName): void
    {
        $headers = $this->{$attrName};

        if ($headers) {

            $params           = ['attr' => $attrName, 'path' => $headers];
            $isCorrectHeaders = is_array($headers) && CsvReaderHelper::isScalarArray($headers);

            if (!$isCorrectHeaders) {
                $this->addError($attrName, CsvReaderHelper::replacePlaceholders('Incorrect option "{attr}": Headers should be defined as one-dimensional (scalar) array of strings.', $params));
            }
        }

    }

    /**
     * Validates CsvRow class.
     *
     * @param string $attrName
     */
    protected function _validateCsvRowClass(string $attrName): void
    {
        $this->_validateClass($attrName, CsvRow::class);
    }

    /**
     * Validates CsvFileMetaData class.
     *
     * @param string $attrName
     */
    protected function _validateCsvFileMetaDataClass(string $attrName): void
    {
        $this->_validateClass($attrName, CsvFileMetaData::class);
    }

    /**
     * Validates CsvRow class.
     *
     * @param string $attrName
     * @param string $parentClass
     */
    protected function _validateClass(string $attrName, string $parentClass): void
    {
        $classToCheck = $this->{$attrName};

        if ($classToCheck) {

            $params         = ['attr' => $attrName, 'class' => $classToCheck, 'correctClass' => $parentClass];
            $isCorrectClass = CsvReaderHelper::isInstanceOf($classToCheck, $parentClass);

            if (!$isCorrectClass) {
                $this->addError($attrName, CsvReaderHelper::replacePlaceholders('Incorrect option "{attr}": Specified class "{class}" should be extended from "{correctClass}".', $params));
            }
        }

    }
}