<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\models\queries;

use common\helpers\DbHelper;
use common\scheduler\models\SchedulerTaskProcess;

/**
 * Class SchedulerTaskProcessQuery describes ActiveQuery class for the SchedulerTaskProcess.
 * This class contains QueryScopes for convenient query building.
 *
 * @method \common\scheduler\models\SchedulerTaskProcess|array|null   one($db = null)   a single row of query result. Depending on the setting of [[asArray]], the query result may be either an array or an ActiveRecord object. Null will be returned if the query results in nothing.
 * @method \common\scheduler\models\SchedulerTaskProcess[]|array      all($db = null)   the query results. Depending on the setting of [[asArray]], the query result may be either an array of arrays or an array of ActiveRecord objects. If the query results in nothing, an empty array will be returned.
 */
class SchedulerTaskProcessQuery extends \common\components\ActiveQuery
{
    /**
     * QueryScope: Add search condition by specified taskId.
     *
     * @param $taskId
     *
     * @return $this
     */
    public function byTask($taskId)
    {
        return $this->addWhere(['task_id' => $taskId]);
    }

    /**
     * QueryScope: Add search condition to match only last scheduled task processes.
     *
     * @return $this
     */
    public function onlyLastScheduledTaskProcesses()
    {
        $queryLastScheduledTasks = SchedulerTaskProcess::find()
            ->groupBy(SchedulerTaskProcess::ATTR_TASK_ID)
            ->select([
                'taskId'          => SchedulerTaskProcess::ATTR_TASK_ID,
                'lastScheduledAt' => DbHelper::exprMax(SchedulerTaskProcess::ATTR_CREATED_AT),
            ]);

        $queryLastLogsProcessIds = SchedulerTaskProcess::find()
            ->innerJoin(['lastScheduledTasks' => $queryLastScheduledTasks],
                SchedulerTaskProcess::colName(SchedulerTaskProcess::ATTR_TASK_ID) . ' = lastScheduledTasks.taskId' .
                ' AND ' . SchedulerTaskProcess::colName(SchedulerTaskProcess::ATTR_CREATED_AT) . ' = lastScheduledTasks.lastScheduledAt'
            )
            ->selectId();

        return $this->byId($queryLastLogsProcessIds);       // Wrap using sub-query to isolate side-effects of JOIN
    }

    /**
     * Adds {@link SchedulerTaskProcess::id} to SELECT statement.
     *
     * @return SchedulerTaskProcessQuery
     */
    public function selectId()
    {
        return $this->addSelect(['id']);
    }
}