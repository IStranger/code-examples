# Yet Another CSV Reader

Yet another CSV reader. It works as component of Yii application and uses some Yii helpers and common classes.

Key features:
 - Uses interfaces `ICsvReader` and `ICsvRow` to reduce dependencies from data source.
 - Uses iterators to allow to read large data sources.



## Usage

```php
class SomeClass {

    /**
     * Default configuration for CSV reader.
     *
     * @return array
     */
    protected function _getDefaultCsvReaderOptions(): array
    {
        return [
            // 'csvDelimiter' => '|',
        ];
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

        return new CsvReader($options);
    }
}
```