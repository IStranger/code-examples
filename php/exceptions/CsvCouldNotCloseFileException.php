<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv\exceptions;

/**
 * The exception is thrown if reader could not close CSV file.
 */
class CsvCouldNotCloseFileException extends CsvReaderException
{

}