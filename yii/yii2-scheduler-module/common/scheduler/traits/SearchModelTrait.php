<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\traits;

use common\components\ActiveQuery;
use backend\components\ActiveDataProvider;
use common\helpers\ArrayHelper;

/**
 * The trait contains useful methods for the search models.
 */
trait SearchModelTrait
{
    /**
     * @var array List of attributes excluded from search/validation.
     */
    public $excludeAttributes = [];

    /**
     * @var array Default DataProvider config.
     */
    public $defaultProviderConfig = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $attrNames = ArrayHelper::removeByValues($this->attributes(), $this->excludeAttributes);

        return [
            [$attrNames, self::VALIDATOR_SAFE],     // All validators are disabled by default. Override teh methods to change this behavior.
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return \yii\base\Model::scenarios();
    }

    /**
     * Returns prepared data provider.
     *
     * @param $params
     *
     * @return ActiveDataProvider
     *
     * @deprecated  This method is only for DynaGrid (to apply saved filters).
     *              Use {@link IProviderPreparer::prepareDataProvider} instead of it.
     */
    public function search($params): ActiveDataProvider
    {
        return $this->prepareDataProvider($params);
    }

    /**
     * Creates data provider from the specified query and config.
     *
     * @param ActiveQuery $query
     * @param array       $config Additional config.
     *
     * @return ActiveDataProvider
     */
    protected function _createDataProvider(ActiveQuery $query, array $config = []): ActiveDataProvider
    {
        $options = ['query' => $query] + $config + $this->defaultProviderConfig;

        return new ActiveDataProvider($options);
    }

    /**
     * Creates "empty" data provider (without search conditions).
     * It is useful in the case of incorrect validation result of the search model.
     *
     * @param array $config Default config.
     *
     * @return ActiveDataProvider
     */
    protected function _createEmptyDataProvider(array $config = []): ActiveDataProvider
    {
        return $this->_createDataProvider(static::find(), $config);
    }

    /**
     * Populates the model and runs validation.
     *
     * @param array $queryParams
     *
     * @return bool              Result of validation.
     */
    protected function _loadAndValidate(array $queryParams): bool
    {
        return $this->load($queryParams) && $this->validate();
    }
}