# Yet Another CSV Reader

The reader works as a component of Yii application and uses some Yii helpers and common classes.
However, library has a weak dependence on the Yii core and may be easily ported to any another application.


## Key features

 - Uses interfaces `ICsvReader` and `ICsvRow` to reduce dependencies on data source.
 - Uses iterators to read large data sources.
 - Internal model validation works via `IValidatable` interface.



## Usage

```php
class SomeClass {

    /**
     * Parses rows from specified CSV file.
     *
     * @param string $csvPath
     *
     * @return array In format: [  ['column1' => 'val1', 'column2' => 'val2'], ... ]
     */
    public function parseRows(string $csvPath): array {

        // Prepare options
        $csvReader = $this->_prepareBaseCsvReader($csvPath);


        // Read Csv
        $result = [];
        $errors = [];

        foreach ($csvReader->getIterator() as $csvRow) {

            $hasErrors = $csvRow->hasErrors();
            $lineNum   = $csvRow->getLineNumber();

            if ($hasErrors) {
                $errors[$lineNum] = 'Line Parsing error: ' . join(', ', $csvRow->getErrors());
            } else {
                $result[$lineNum] = $csvRow->getValuesIndexedByHeaders();
            }

        }

        // Log errors
        // $this->_logError('Parsing errors: ' . json_encode($errors));

        return $result;
    }

    /**
     * Prepares CVS reader for the specified path.
     *
     * @param string $csvPath
     *
     * @return \common\csv\interfaces\ICsvReader
     */
    protected function _prepareBaseCsvReader(string $csvPath): ICsvReader
    {
        $options               = new CsvReaderOptions($this->_getDefaultCsvReaderOptions());
        $options->csvPath      = $csvPath;
        $options->skipFirstRow = true;
        // $options->headers              = [];
        // $options->csvDelimiter         = "\t";        // by default ","
        // $options->csvEnclosure         = "'";         // by default '"'
        // $options->csvEscape            = '\\';
        // $options->csvRowClass          = CsvRow::class;
        // $options->csvFileMetaDataClass = CsvFileMetaData::class;

        return new CsvReader($options);
    }
}
```