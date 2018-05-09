<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\models\queries;

use common\helpers\DbHelper;

/**
 * Class SchedulerProcessQuery describes ActiveQuery class for the SchedulerProcess.
 * This class contains QueryScopes for convenient query building.
 *
 * @method \common\scheduler\models\SchedulerProcess|array|null   one($db = null)   a single row of query result. Depending on the setting of [[asArray]], the query result may be either an array or an ActiveRecord object. Null will be returned if the query results in nothing.
 * @method \common\scheduler\models\SchedulerProcess[]|array      all($db = null)   the query results. Depending on the setting of [[asArray]], the query result may be either an array of arrays or an array of ActiveRecord objects. If the query results in nothing, an empty array will be returned.
 */
class SchedulerProcessQuery extends \common\components\ActiveQuery
{
    /**
     * QueryScope: Adds search condition by {@link SchedulerProcess::type}.
     *
     * @param int|mixed $type
     *
     * @return $this
     */
    public function byType($type)
    {
        return $this->andWhere(['type' => $type]);
    }

    /**
     * QueryScope: Adds search condition by {@link SchedulerProcess::created_at}.
     *
     * @param string $runtime       Date .
     * @param string $dbDateFormat  Db datetime format,
     *                              for example: {@link DbHelper::FORMAT_MYSQL_DATETIME}.
     *
     * @return $this
     */
    public function byRuntimeFormat($runtime, $dbDateFormat)
    {
        $exprCreatedAt = DbHelper::exprDateFormat('created_at', $dbDateFormat);

        return $this->andWhere(['=', $exprCreatedAt, $runtime]);
    }
}