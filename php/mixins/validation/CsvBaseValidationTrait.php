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
 * Base validation class.
 */
trait CsvBaseValidationTrait
{
    /**
     * Checks whether specified property instantiates certain class.
     * NOTE: `null` value will be skipped.
     *
     * @param string $propName
     * @param string $instanceOfClass
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkPropClass(string $propName, string $instanceOfClass): void
    {
        $classToCheck = $this->{$propName};

        if ($classToCheck) {

            $params         = ['attr' => $propName, 'class' => $classToCheck, 'correctClass' => $instanceOfClass];
            $isCorrectClass = CsvReaderHelper::isInstanceOf($classToCheck, $instanceOfClass);

            if (!$isCorrectClass) {

                $msg = CsvReaderHelper::replacePlaceholders('Incorrect option "{attr}": It should be instantiated from "{correctClass}" class ("{class}" class was specified).', $params);
                throw new CsvInvalidParamException($msg);
            }
        }

    }

    /**
     * Checks whether specified property is not null.
     *
     * @param string $propName
     *
     * @throws CsvInvalidParamException
     */
    protected function _checkPropRequired(string $propName)
    {
        $valueToCheck = $this->{$propName};
        $isIncorrect  = is_null($valueToCheck) || $valueToCheck === '';

        if ($isIncorrect) {

            $msg = CsvReaderHelper::replacePlaceholders('Missing required option "{attr}".', ['attr' => $propName]);
            throw new CsvInvalidParamException($msg);
        }
    }
}