<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\presenters;

use Yii;
use common\helpers\DbHelper;
use common\helpers\TimeHelper;
use common\helpers\ArrayHelper;
use common\scheduler\SchedulerService;
use common\scheduler\models\SchedulerTask;
use common\scheduler\models\SchedulerProcess;
use common\scheduler\models\SchedulerTaskProcess;
use common\scheduler\presenters\models\TaskInfo;
use common\scheduler\presenters\models\LastExecutionInfo;

/**
 * The class prepares data for displaying.
 * For example, special data structures and formatters for dropDownLists etc.
 * Place view-specific logic to the Presenter.
 */
class SchedulerPresenter extends \common\components\AbstractPresenter
{
    /**
     * Returns last scheduled timestamp.
     *
     * @return int|null
     */
    public function getLastScheduledTimestamp(): ?int
    {
        return Yii::$app->schedulerService->getLastScheduledTimestamp(SchedulerProcess::TYPE_REGULAR);
    }

    /**
     * Returns **previous** run dates.
     *
     * @param SchedulerTask $model
     *
     * @return string[]|null    List of formatted dates, or =null if cron expression could not be parsed.
     * @throws \Exception
     */
    public function getPrevRunDates(SchedulerTask $model): ?array
    {
        return Yii::$app->schedulerService->parsePreviousCronRunDates($model->cron_time, 5, SchedulerService::TIME_FORMAT, false);
    }

    /**
     * Returns **next** run dates.
     *
     * @param SchedulerTask $model
     *
     * @return string[]|null    List of formatted dates, or =null if cron expression could not be parsed.
     * @throws \Exception
     */
    public function getNextRunDates(SchedulerTask $model): ?array
    {
        return Yii::$app->schedulerService->parseNextCronRunDates($model->cron_time, 5, SchedulerService::TIME_FORMAT, false);
    }

    /**
     * Prepares last execution info.
     *
     * @param int|null $lastScheduledTimestamp
     *
     * @return LastExecutionInfo
     */
    public function prepareLastExecInfo(?int $lastScheduledTimestamp): LastExecutionInfo
    {
        $model            = new LastExecutionInfo();
        $model->timestamp = $lastScheduledTimestamp;

        if (is_int($model->timestamp)) {
            $model->at       = DbHelper::formatDatetime($lastScheduledTimestamp);
            $model->ago      = TimeHelper::formatTimeDiffAsHumanReadable($lastScheduledTimestamp, DbHelper::getCurrentTimestamp(true));
            $model->timeDiff = DbHelper::getCurrentTimestamp(true) - strtotime(DbHelper::formatDatetime($lastScheduledTimestamp));
        }

        return $model;
    }

    /**
     * Factory: Creates function to format task info.
     *
     * @return \Closure
     */
    public function factoryTaskInfoFormatter(): \Closure
    {
        return function (SchedulerTask $task): TaskInfo {

            $model              = new TaskInfo();
            $model->lastProcess = $model;

            if ($task->lastSchedulerTaskProcess) {
                $model->alertClass         = $this->_getAlertClass($task->lastSchedulerTaskProcess->status);
                $model->startedAt          = $task->lastSchedulerTaskProcess->created_at;
                $model->startedAtFormatted = TimeHelper::formatTimeDiffAsHumanReadable($model->startedAt) . ' ago';
                $model->statusAlias        = $task->lastSchedulerTaskProcess->getStatusAlias();
            }

            return $model;
        };
    }

    /**
     * Returns status map.
     *
     * @param int $taskProcessStatus
     *
     * @return string
     */
    protected function _getAlertClass(int $taskProcessStatus): string
    {
        $statusMap = [
            SchedulerTaskProcess::STATUS_DRAFT   => 'info',
            SchedulerTaskProcess::STATUS_PROCESS => 'info',
            SchedulerTaskProcess::STATUS_SUCCESS => 'success',
            SchedulerTaskProcess::STATUS_ERROR   => 'danger',
        ];

        return ArrayHelper::getValue($statusMap, $taskProcessStatus, 'info');
    }
}