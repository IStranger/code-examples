<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\models\search;

use yii\data\DataProviderInterface;
use common\scheduler\traits\SearchModelTrait;
use common\scheduler\models\queries\SchedulerTaskProcessQuery;
use common\scheduler\models\SchedulerTaskProcess;

/**
 * The model represents search model for the task processes.
 */
class SchedulerTaskProcessSearch extends SchedulerTaskProcess
{
    use SearchModelTrait;

    /**
     * @var array Default DataProvider config.
     */
    protected const DEFAULT_PROVIDER_CONFIG = [
        'sort'       => ['defaultOrder' => ['created_at' => SORT_DESC]],
        'pagination' => ['pageSize' => 20],
    ];

    /**
     * Prepares data provider.
     *
     * @param array $params
     * @param int   $taskId Task Id.
     *
     * @return DataProviderInterface
     */
    public function prepareDataProvider(array $params, int $taskId): DataProviderInterface
    {
        $isValidated = $this->_loadAndValidate($params);

        if (!$isValidated) {
            return $this->_createEmptyDataProvider(self::DEFAULT_PROVIDER_CONFIG);       // Skip due to incorrect filter values
        }


        // Prepare search query
        $query = $this
            ->bySearchFilters(static::find())
            ->byTask($taskId);

        return $this->_createDataProvider($query, self::DEFAULT_PROVIDER_CONFIG);
    }


    /**
     * Applies search filters by current search model.
     *
     * @param SchedulerTaskProcessQuery $query
     *
     * @return SchedulerTaskProcessQuery
     */
    protected function bySearchFilters(SchedulerTaskProcessQuery $query): SchedulerTaskProcessQuery
    {
        $query->andFilterWhere([
            'id'                 => $this->id,
            'process_id'         => $this->process_id,
            'task_id'            => $this->task_id,
            'status'             => $this->status,
            'created_by_user_id' => $this->created_by_user_id,
        ]);

        $query
            ->andFilterCompare('start_delay', $this->start_delay);

        $query
            ->andFilterWhere(['like', 'result', $this->result])
            ->andFilterWhere(['like', 'log', $this->log])
            ->andFilterWhere(['like', 'should_be_started_at', $this->should_be_started_at])
            ->andFilterWhere(['like', 'created_at', $this->created_at])
            ->andFilterWhere(['like', 'updated_at', $this->updated_at]);

        return $query;
    }
}
