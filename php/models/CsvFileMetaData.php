<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\models;

use common\csv\interfaces\IValidatable;
use common\csv\mixins\validation\CsvMetaDataValidationTrait;

/**
 * This class describes metadata of CSV file.
 */
class CsvFileMetaData implements IValidatable
{
    use CsvMetaDataValidationTrait;

    /**
     * @var resource CSV file handle.
     */
    public $handle;

    /**
     * @var string[] Headers of file.
     */
    public $headers;

    /**
     * @var string Size of file.
     */
    public $fileSize;
}