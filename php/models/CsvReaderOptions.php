<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\models;

use common\csv\CsvRow;
use common\csv\interfaces\IValidatable;
use common\csv\mixins\validation\CsvReaderOptionValidationTrait;

/**
 * The class describes CsvReaderOptions.
 *
 * NOTE: the class extends common Yii model **only** for validation purposes.
 * It will be used in validation trait to implement {@link IValidatable}.
 */
class CsvReaderOptions extends \common\components\Model implements IValidatable
{
    use CsvReaderOptionValidationTrait;

    /**
     * @var bool CSV path.
     */
    public $csvPath;

    /**
     * @var bool Skip first row. If =true, data iterator will not return first row.
     */
    public $skipFirstRow;

    /**
     * @var string[] If not specified, headers will be parsed from the first row.
     *                Define it explicitly if CSV file does not contain (or contains incorrect) headers.
     */
    public $headers;

    /**
     * @var string CsvRow class (should be extended from {@link \common\csv\CsvRow}).
     */
    public $csvRowClass = CsvRow::class;

    /**
     * @var string CsvFileMetaData class (should be extended from {@link \common\csv\models\CsvFileMetaData}).
     */
    public $csvFileMetaDataClass = CsvFileMetaData::class;

    /**
     * @var string CSV field delimiter (one character only).
     */
    public $csvDelimiter = ',';

    /**
     * @var string CSV field enclosure character (one character only)..
     */
    public $csvEnclosure = '"';

    /**
     * @var string CSV escape character (one character only). Defaults as a backslash.
     */
    public $csvEscape = '\\';

}