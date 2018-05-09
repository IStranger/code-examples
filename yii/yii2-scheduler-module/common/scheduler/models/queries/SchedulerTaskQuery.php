<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\models\queries;

use common\scheduler\models\SchedulerTask;

/**
 * Class SchedulerTaskQuery describes ActiveQuery class for the SchedulerTask.
 * This class contains QueryScopes for convenient query building.
 *
 * @method \common\scheduler\models\SchedulerTask|array|null   one($db = null)   a single row of query result. Depending on the setting of [[asArray]], the query result may be either an array or an ActiveRecord object. Null will be returned if the query results in nothing.
 * @method \common\scheduler\models\SchedulerTask[]|array      all($db = null)   the query results. Depending on the setting of [[asArray]], the query result may be either an array of arrays or an array of ActiveRecord objects. If the query results in nothing, an empty array will be returned.
 */
class SchedulerTaskQuery extends \common\components\ActiveQuery
{
    /**
     * QueryScope: Adds search condition by {@link SchedulerTask::is_active}.
     *
     * @param bool $active
     *
     * @return $this
     */
    public function onlyActive($active = true)
    {
        return $this->andWhere(['is_active' => $active ? 1 : 0]);
    }

    /**
     * Orders rows by {@link SchedulerTask::order}.
     *
     * @param int $sortDirection
     *
     * @return $this
     */
    public function orderByOrderField($sortDirection = SORT_ASC)
    {
        return $this->orderBy(['order' => $sortDirection]);
    }

    /**
     * Adds WITH() with {@link SchedulerTask::REL_LAST_SCHEDULED_TASK_PROCESS}.
     *
     * @return $this
     */
    public function withLastScheduledTaskProcess()
    {
        return $this->with(SchedulerTask::REL_LAST_SCHEDULED_TASK_PROCESS);
    }
}