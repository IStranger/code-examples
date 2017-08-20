<?php
/**
 * Yet another CSV Reader.
 * Created by [istranger] [2017-08-19 12:45]
 */

declare (strict_types=1);

namespace common\csv;

use common\csv\helpers\CsvReaderHelper;
use common\csv\interfaces\IValidatable;
use common\csv\models\CsvFileMetaData;
use common\csv\models\CsvReaderOptions;
use common\csv\exceptions\CsvInvalidParamException;
use common\csv\exceptions\CsvIncorrectFileException;
use common\csv\exceptions\CsvCouldNotCloseFileException;
use common\csv\exceptions\CsvCouldNotOpenFileException;

/**
 * Class CsvReader
 */
class CsvReader implements \common\csv\interfaces\ICsvReader
{
    /**
     * @var CsvReaderOptions
     */
    protected $_options;

    /**
     * CsvReader constructor.
     *
     * @param CsvReaderOptions $csvReaderOptions
     *
     * @throws \common\csv\exceptions\ICsvReaderException
     */
    public function __construct(CsvReaderOptions $csvReaderOptions)
    {
        $this->_initReader($csvReaderOptions);
    }

    /**
     * Returns row iterator. Each iterated value should implement {@link ICsvRow}.
     *
     * @return \Iterator|\common\csv\interfaces\ICsvRow[]
     *
     * @throws CsvIncorrectFileException    In case of any errors with CSV file
     * @throws CsvInvalidParamException        In case of incorrect options.
     * @throws \common\csv\exceptions\ICsvReaderException
     */
    public function getIterator(): iterable
    {
        $options = $this->_getOptions();

        // Prepare CSV file metadata
        $csvMetaData = $this->_createMetaData();
        $this->_populateFileMetaDataFromOptions($this->_getOptions(), $csvMetaData);
        $this->_checkValidatableModel($csvMetaData);

        // Prepare file handle before reading (skip first row etc)
        $this->_prepareFileBeforeReading($options, $csvMetaData);

        // Read file lines
        return $this->_prepareCsvRowIterator($options, $csvMetaData);
    }

    /**
     * Initialized CSV reader.
     *
     * @param CsvReaderOptions $csvReaderOptions
     *
     * @throws exceptions\ICsvReaderException   In case of incorrect options.
     */
    protected function _initReader(CsvReaderOptions $csvReaderOptions): void
    {
        $this->_checkValidatableModel($csvReaderOptions);
        $this->_options = $csvReaderOptions;
    }

    /**
     * Checks specified metadata model.
     *
     * @param IValidatable $model
     *
     * @throws exceptions\ICsvReaderException   In case of any validation errors.
     */
    protected function _checkValidatableModel(IValidatable $model): void
    {
        $model->checkProperties();
    }

    /**
     * Returns CSV reader options.
     *
     * @return CsvReaderOptions
     */
    protected function _getOptions(): CsvReaderOptions
    {
        return $this->_options;
    }

    /**
     * Prepares CsvRow iterator for read of file lines.
     *
     * @param CsvReaderOptions $options
     * @param CsvFileMetaData  $csvMetaData
     *
     * @return \Generator  Iterator that iterates CsvRow.
     *
     * @throws CsvInvalidParamException                         In case of invalid resource handle.
     * @throws \common\csv\exceptions\ICsvReaderException
     */
    protected function _prepareCsvRowIterator(CsvReaderOptions $options, CsvFileMetaData $csvMetaData): iterable
    {
        $lineNumber = $options->skipFirstRow
            ? 1
            : 0;

        while (!feof($csvMetaData->handle)) {

            $lineNumber++;
            $parsingErrors = [];

            $values = $this->_readLine($csvMetaData->handle, $options);

            // Parsing errors
            if ($values === null) {
                $parsingErrors[] = 'Row cannot be parsed as CSV.';
            }

            // Yield CSV row
            yield $this->_createCsvRow([
                'csvFileMetaData' => $csvMetaData,
                'values'          => $values,
                'lineNumber'      => $lineNumber,
                'parsingErrors'   => $parsingErrors,
            ]);
        }

        // Close handle
        $this->_closeHandle($csvMetaData);
    }

    /**
     * Reads single line and parses it as CSV.
     *
     * @param resource         $handle
     * @param CsvReaderOptions $options
     *
     * @return array|null       Array of values or `null` if line cannot be parsed as CSV.
     *
     * @throws CsvInvalidParamException In case of invalid resource handle.
     */
    protected function _readLine($handle, CsvReaderOptions $options): ?array
    {
        $values = fgetcsv($handle, 0, $options->csvDelimiter, $options->csvEnclosure, $options->csvEscape);

        // Fix the case of blank lines
        if ($values === [null]) {
            return [''];
        }

        // Normal values
        if (is_array($values)) {
            return $values;
        }

        // Invalid handle
        if ($values === null) {
            throw new CsvInvalidParamException('Invalid resource handle.');
        }

        return null;        // Parsing errors. For example: ($values === false)
    }

    /**
     * Prepares file before reading. For example skip first (depending on option) row etc.
     *
     * @param CsvReaderOptions $options
     * @param CsvFileMetaData  $csvMetaData
     *
     * @throws \common\csv\exceptions\ICsvReaderException
     */
    protected function _prepareFileBeforeReading(CsvReaderOptions $options, CsvFileMetaData $csvMetaData): void
    {
        // Skip first row
        if ($options->skipFirstRow) {
            $this->_readLine($csvMetaData->handle, $options);
        }
    }

    /**
     * Populates specified model.
     *
     * @param CsvReaderOptions $options
     * @param CsvFileMetaData  $metaData
     *
     * @throws CsvCouldNotOpenFileException    If method could not open the file.
     * @throws \common\csv\exceptions\ICsvReaderException
     */
    protected function _populateFileMetaDataFromOptions(CsvReaderOptions $options, CsvFileMetaData $metaData): void
    {
        $csvPath = $options->csvPath;

        $metaData->handle   = $this->_openFile($csvPath);
        $metaData->fileSize = filesize($csvPath);


        // Prepare headers
        if ($options->headers) {
            $metaData->headers = $options->headers;
        } else {
            $tmpHandle         = $this->_openFile($csvPath);
            $metaData->headers = $this->_readLine($tmpHandle, $options);
        }

        $metaData->headers = CsvReaderHelper::trimStrings($metaData->headers);
    }

    /**
     * Opens specified file.
     *
     * @param string $csvPath
     *
     * @return bool|resource
     * @throws CsvCouldNotOpenFileException    If method could not open the file.
     */
    protected function _openFile($csvPath)
    {
        // Open file
        $handle = fopen($csvPath, 'r');

        if ($handle === false) {
            throw new CsvCouldNotOpenFileException(CsvReaderHelper::replacePlaceholders('Could not open file "{path}".', ['path' => $csvPath]));
        }

        return $handle;
    }

    /**
     * Closes CSV file handle.
     *
     * @param CsvFileMetaData $metaData
     *
     * @throws CsvCouldNotCloseFileException
     */
    protected function _closeHandle(CsvFileMetaData $metaData): void
    {
        $isSuccess = fclose($metaData->handle);

        if (!$isSuccess) {
            throw new CsvCouldNotCloseFileException('Could not close CSV file.');
        }
    }

    /**
     * Creates new file metadata instance.
     *
     * @return CsvFileMetaData
     */
    protected function _createMetaData()
    {
        $className = $this->_getOptions()->csvFileMetaDataClass;

        return new $className();
    }

    /**
     * Creates new CsvRow instance.
     *
     * @param array $config CsvRow configuration.
     *
     * @return CsvRow
     */
    protected function _createCsvRow($config)
    {
        $className = $this->_getOptions()->csvRowClass;

        return new $className($config);
    }
}