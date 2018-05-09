<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\models\search;

use yii\data\DataProviderInterface;
use common\scheduler\models\queries\SchedulerTaskQuery;
use common\scheduler\traits\SearchModelTrait;

/**
 * The model represents search model for the tasks.
 */
class SchedulerTaskSearch extends \common\scheduler\models\SchedulerTask
{
    use SearchModelTrait;

    /**
     * Prepares data provider.
     *
     * @param array $params
     *
     * @return DataProviderInterface
     */
    public function prepareDataProvider(array $params): DataProviderInterface
    {
        $isValidated = $this->_loadAndValidate($params);

        if (!$isValidated) {
            return $this->_createEmptyDataProvider();       // Skip due to incorrect filter values
        }


        // Prepare search query
        $query = $this
            ->bySearchFilters(static::find())
            ->orderByOrderField()
            ->withLastScheduledTaskProcess();

        return $this->_createDataProvider($query);
    }

    /**
     * Applies search filters by current search model.
     *
     * @param SchedulerTaskQuery $query
     *
     * @return SchedulerTaskQuery
     */
    protected function bySearchFilters(SchedulerTaskQuery $query): SchedulerTaskQuery
    {
        $query->andFilterWhere([
            'id'                       => $this->id,
            'is_active'                => $this->is_active,
            'as_runner_script'         => $this->as_runner_script,
            'allowable_time_deviation' => $this->allowable_time_deviation,
            'created_at'               => $this->created_at,
            'created_by_user_id'       => $this->created_by_user_id,
        ]);

        $query
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'cron_time', $this->cron_time])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'options', $this->options]);

        return $query;
    }
}
