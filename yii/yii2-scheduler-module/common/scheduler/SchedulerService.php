<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler;

use Cron\CronExpression;
use Yii;
use common\helpers\DbHelper;
use common\helpers\TimeHelper;
use common\helpers\StringHelper;
use common\scheduler\models\SchedulerTask;
use common\scheduler\models\SchedulerProcess;
use common\scheduler\models\SchedulerTaskProcess;
use common\scheduler\interfaces\ISchedulerTask;
use common\scheduler\exceptions\CouldNotFindException;
use common\scheduler\exceptions\CouldNotSaveException;
use console\runnerScripts\StartSchedulerTask;

/**
 * The main scheduler service.
 */
class SchedulerService extends \common\components\Component
{
    /**
     * Date format that used for time comparison (without seconds).
     */
    const TIME_FORMAT    = 'Y-m-d H:i';
    const DB_TIME_FORMAT = '%Y-%m-%d %H:%i';

    /**
     * Runs all necessary tasks.
     *
     * NOTE: this method should be executed every minute.
     */
    public function runAllTasks(): void
    {
        if ($this->_isAllowStartAllTasks()) {

            // Prepare tasks
            $schedulerTasks = SchedulerTask::find()
                ->onlyActive()
                ->all();

            $this->runTasks($schedulerTasks);

        } else {

            $this->_logError('Prevented excess starting all tasks.', [], 'SchedulerService.runAllTasks');

        }
    }

    /**
     * Runs specified tasks.
     *
     * @param SchedulerTask[] $schedulerTasks
     * @param bool            $forceStarting If =true, task will be started right now. It will be marked as "manual" start.
     *
     * @throws CouldNotSaveException
     */
    public function runTasks(array $schedulerTasks, $forceStarting = false): void
    {

        // Create scheduler process draft
        $schedulerProcess         = new SchedulerProcess();
        $schedulerProcess->status = SchedulerProcess::STATUS_DRAFT;
        $schedulerProcess->type   = $forceStarting
            ? SchedulerProcess::TYPE_MANUAL
            : SchedulerProcess::TYPE_REGULAR;
        $schedulerProcess->log    = $this->_prepareLogMsg('Created draft.');
        $this->_saveOrThrowException($schedulerProcess, true, 'Could not create scheduler process draft.');

        try {

            // Prepare to process
            $schedulerProcess->status = SchedulerProcess::STATUS_PROCESS;
            $schedulerProcess->log    .= $this->_prepareLogMsg('Started process of ' . count($schedulerTasks) . ' active tasks.');
            $this->_saveOrThrowException($schedulerProcess, true, 'Could not update scheduler process (starting).');

            // Start tasks processing
            foreach ($schedulerTasks as $schedulerTask) {

                $this->_processTask($schedulerProcess, $schedulerTask, $forceStarting);

            }

            // Complete
            $schedulerProcess->status = SchedulerProcess::STATUS_SUCCESS;
            $schedulerProcess->log    .= $this->_prepareLogMsg('Successfully completed.');
            $this->_saveOrThrowException($schedulerProcess, true, 'Could not update scheduler process (completion).');

        } catch (\Throwable $exception) {

            $logMsg = 'Scheduler error:' . StringHelper::convertExceptionToStr($exception);
            $this->_logError($logMsg, [], 'SchedulerService.runAllTasks');

            $schedulerProcess->status = SchedulerProcess::STATUS_ERROR;
            $schedulerProcess->log    .= $this->_prepareLogMsg($logMsg);
            $this->_saveOrThrowException($schedulerProcess, true, 'Could not update scheduler process (exception).');

        }

    }

    /**
     * Returns time when last executed scheduler.
     *
     * @param int|null $type
     *
     * @return int|null Timestamp or =null if not scheduled.
     */
    public function getLastScheduledTimestamp($type = null): ?int
    {
        $queryLastScheduledAt = SchedulerProcess::find()
            ->select(DbHelper::exprMax(SchedulerProcess::ATTR_CREATED_AT));

        if ($type) {
            $queryLastScheduledAt->byType($type);
        }

        $lastScheduledAt = $queryLastScheduledAt->scalar();

        return $lastScheduledAt
            ? strtotime($lastScheduledAt)
            : null;
    }

    /**
     * Parses cron expression and retrieves **next** run dates.
     *
     * @param string $timeInCronFormat
     * @param int    $runDatesQty      Number result items.
     * @param string $resultDateFormat Format of result dates.
     * @param bool   $throwIfException
     *
     * @return null|string[] List of formatted dates, or =null if expression could not be parsed.
     * @throws \Exception
     */
    public function parseNextCronRunDates($timeInCronFormat, $runDatesQty = 5, $resultDateFormat = self::TIME_FORMAT, $throwIfException = true): ?array
    {
        return $this->_parseCronRunDates($timeInCronFormat, $runDatesQty, true, $resultDateFormat);
    }

    /**
     * Parses cron expression and retrieves **previous** run dates.
     *
     * @param string $timeInCronFormat
     * @param int    $runDatesQty      Number result items.
     * @param string $resultDateFormat Format of result dates.
     * @param bool   $throwIfException
     *
     * @return null|string[] List of formatted dates, or =null if expression could not be parsed.
     * @throws \Exception
     */
    public function parsePreviousCronRunDates($timeInCronFormat, $runDatesQty = 5, $resultDateFormat = self::TIME_FORMAT, $throwIfException = true): ?array
    {
        return $this->_parseCronRunDates($timeInCronFormat, $runDatesQty, false, $resultDateFormat);
    }

    /**
     * Parses cron expression and retrieves next (or previous) run dates.
     *
     * @param string $timeInCronFormat
     * @param int    $runDatesQty      Number result items.
     * @param bool   $next             If =true, will be returned next run dates, otherwise - previous.
     * @param string $resultDateFormat Format of result dates.
     * @param bool   $throwIfException
     *
     * @return null|string[] List of formatted dates, or =null if expression could not be parsed.
     * @throws \Exception
     */
    protected function _parseCronRunDates($timeInCronFormat, $runDatesQty, $next = true, $resultDateFormat = self::TIME_FORMAT, $throwIfException = true): ?array
    {
        /** @var \DateTime $runDate */

        try {

            $cronExpr    = $this->_prepareCronExpression($timeInCronFormat);
            $currentTime = DbHelper::formatDatetime(DbHelper::getCurrentTimestamp(true));

            $runDates = $cronExpr->getMultipleRunDates($runDatesQty, $currentTime, !$next, false);

            $formattedDates = [];
            foreach ($runDates as $runDate) {
                $formattedDates[] = $runDate->format($resultDateFormat);
            }

        } catch (\Throwable $exception) {

            $this->_logError(Yii::t('app', 'Exception at parsing cron expression: ' . StringHelper::convertExceptionToStr($exception)));

            if ($throwIfException) {
                throw $exception;
            }

        }


        return $formattedDates;
    }

    /**
     * Checks whether all tasks can be started. Prevents starting more than one time at minute.
     *
     * @return bool
     */
    protected function _isAllowStartAllTasks(): bool
    {
        $currentTime              = $this->_getCurrentTime();
        $queryScheduledAtThisTime = SchedulerProcess::find()
            ->select('id')
            ->byType(SchedulerProcess::TYPE_REGULAR)
            ->byRuntimeFormat($currentTime, self::DB_TIME_FORMAT);

        return !$queryScheduledAtThisTime->exists();
    }

    /**
     * Processes specified task.
     *
     * @param SchedulerProcess $schedulerProcess
     * @param SchedulerTask    $schedulerTask
     * @param bool             $forceStarting If =true, task will be started right now.
     *
     * @throws CouldNotSaveException
     */
    protected function _processTask(SchedulerProcess $schedulerProcess, SchedulerTask $schedulerTask, $forceStarting = false): void
    {
        try {

            if ($this->_checkIfTaskShouldBeStarted($schedulerTask, $forceStarting)) {

                // When this task should be started
                $shouldBeStartedAt = $this->_normalizeTime($this->_getPreviousRunTime($schedulerTask), DbHelper::FORMAT_DATETIME);
                $currentDelay      = $this->_calculateDelay($shouldBeStartedAt);

                // Create task process draft
                $taskProcess                       = new SchedulerTaskProcess();
                $taskProcess->process_id           = $schedulerProcess->id;
                $taskProcess->task_id              = $schedulerTask->id;
                $taskProcess->status               = SchedulerTaskProcess::STATUS_DRAFT;
                $taskProcess->start_delay          = $currentDelay;
                $taskProcess->should_be_started_at = $shouldBeStartedAt;
                $taskProcess->log                  .= $this->_prepareLogMsg('Prepared to start. Start delay = ' . $currentDelay . ' sec.');
                $this->_saveOrThrowException($taskProcess, true, 'Could not create task draft. Possible errors: {errors}.');

                // Start task
                if ($schedulerTask->as_runner_script) {
                    $this->_startTaskExecutionAsRunnerScript($taskProcess);
                } else {
                    $this->startTaskExecution($taskProcess);
                }
            }

        } catch (\Throwable $exception) {

            $logMsg = 'Task #' . $schedulerTask->id . ' processing error: ' . StringHelper::convertExceptionToStr($exception);
            $this->_logError($logMsg);
            $schedulerProcess->log .= $this->_prepareLogMsg($logMsg);
            $this->_saveOrThrowException($schedulerProcess, true, 'Could not update scheduler process (task processing error).');

        }

    }

    /**
     * Starts execution of specified task (in runner script).
     *
     * @param SchedulerTaskProcess $taskProcess
     */
    protected function _startTaskExecutionAsRunnerScript(SchedulerTaskProcess $taskProcess): void
    {
        StartSchedulerTask::startScriptExecution([
            StartSchedulerTask::TASK_PROCESS_ID => $taskProcess->id,
        ]);
    }

    /**
     * Starts execution of specified task.
     *
     * @param SchedulerTaskProcess $taskProcess
     *
     * @throws \Exception
     */
    public function startTaskExecution(SchedulerTaskProcess $taskProcess): void
    {
        try {

            // Check params
            if (!$taskProcess->task) {
                throw new CouldNotFindException('Could not find related task.');
            }


            // Start process
            $taskProcess->status = SchedulerTaskProcess::STATUS_PROCESS;
            $taskProcess->log    .= $this->_prepareLogMsg('Started task execution.');
            $this->_saveOrThrowException($taskProcess, true, 'Could not start task process. Possible errors: {errors}.');


            // Start task
            $result = $this->_createTask($taskProcess->task)
                ->run();


            // Save execution result
            $taskProcess->status = SchedulerTaskProcess::STATUS_SUCCESS;
            $taskProcess->result = $result;
            $taskProcess->log    .= $this->_prepareLogMsg('Task has done successfully.');
            $this->_saveOrThrowException($taskProcess, true, 'Could not update task process (successfully completion). Possible errors: {errors}.');


        } catch (\Throwable $exception) {

            $logMsg = 'Scheduler task process error: ' . StringHelper::convertExceptionToStr($exception);
            $this->_logError($logMsg);

            $taskProcess->status = SchedulerTaskProcess::STATUS_ERROR;
            $taskProcess->log    .= $this->_prepareLogMsg($logMsg);
            $this->_saveOrThrowException($taskProcess, true, 'Could not update task process (exception). Possible errors: {errors}.');

            throw $exception;

        }

    }

    /**
     * Creates scheduler task.
     *
     * @param SchedulerTask $model
     *
     * @return ISchedulerTask
     */
    protected function _createTask(SchedulerTask $model): ISchedulerTask
    {
        $className = $model->class;

        return new $className($model->options);
    }

    /**
     * Saves specified model or throws exception with the specified message.
     *
     * @param \yii\db\ActiveRecord $model
     * @param bool                 $runValidation
     * @param string               $exceptionMsg
     *
     * @throws CouldNotSaveException
     * @throws \Exception
     */
    protected function _saveOrThrowException(\yii\db\ActiveRecord $model, $runValidation = true, $exceptionMsg = 'Could not save {modelName}. Possible errors: {errors}'): void
    {
        $isSuccess = $model->save($runValidation);

        if (!$isSuccess) {
            throw new CouldNotSaveException(Yii::t('app', $exceptionMsg, [
                'modelName' => $model->formName(),
                'errors'    => join(', ', $model->getFirstErrors()),
            ]));
        }
    }

    /**
     * Checks if task should be started.
     *
     * @param SchedulerTask $task
     * @param bool          $forceStarting
     *
     * @return bool
     */
    protected function _checkIfTaskShouldBeStarted(SchedulerTask $task, $forceStarting): bool
    {
        // Force starting
        if ($forceStarting) {
            return true;
        }

        // Regular starting
        if ($task->is_active) {

            $prevRunTime     = $this->_getPreviousRunTime($task);
            $hasPerformed    = $this->_checkIfTaskStartedAt($task, $prevRunTime);
            $shouldBeStarted = !$hasPerformed && !$this->_checkIfRuntimeHasExpired($task, $prevRunTime);

            return $shouldBeStarted;
        }

        return false;
    }


    /**
     * Checks if specified run time has expired.
     *
     * @param SchedulerTask $task
     * @param string|int    $runTimeToCheck Time in timestamp or string representation.
     *
     * @return bool
     */
    protected function _checkIfRuntimeHasExpired(SchedulerTask $task, $runTimeToCheck): bool
    {
        $deviationSeconds = intval($task->allowable_time_deviation);
        $currentDelay     = $this->_calculateDelay($runTimeToCheck);

        return $currentDelay > $deviationSeconds;
    }

    /**
     * Calculates execution delay for the specified run time.
     *
     * @param string|int $runTime             Time in timestamp or string representation.
     * @param string     $formatForComparison Date format for time comparison.
     *
     * @return int          Number of seconds.
     */
    protected function _calculateDelay($runTime, $formatForComparison = self::TIME_FORMAT): int
    {
        // Current delay from the scheduled start time
        $currentTimestamp  = strtotime($this->_getCurrentTime($formatForComparison));
        $startTimestamp    = strtotime($this->_normalizeTime($runTime, $formatForComparison));
        $correctTimestamps = is_int($currentTimestamp) && is_int($startTimestamp);

        if (!$correctTimestamps) {
            throw new \RuntimeException(Yii::t('app', 'Could not evaluate timestamps.'));
        }

        return $currentTimestamp - $startTimestamp;
    }

    /**
     * Checks if task started at specified run time.
     *
     * @param SchedulerTask $task
     * @param string|int    $runTimeToCheck Time in timestamp or string representation.
     *
     * @return bool
     */
    protected function _checkIfTaskStartedAt(SchedulerTask $task, $runTimeToCheck): bool
    {
        $lastStartedAt = $this->_getLastStartTime($task->id);

        return $lastStartedAt && $this->_isEqualTime($lastStartedAt, $runTimeToCheck);
    }


    /**
     * Returns time when the task was to be performed.
     * If task should be performed right now, method will return current time.
     *
     * @param SchedulerTask $task
     * @param string        $format
     *
     * @return string  Time in specified format.
     */
    protected function _getPreviousRunTime(SchedulerTask $task, $format = self::TIME_FORMAT): string
    {
        $cronExpr        = $this->_prepareCronExpression($task->cron_time);
        $currentTime     = $this->_getCurrentTime($format);
        $prevRunDateTime = $cronExpr->getPreviousRunDate($currentTime, 0, true);

        return $prevRunDateTime->format($format);
    }

    /**
     * Returns formatted current time.
     *
     * @param string $format
     *
     * @return string           Time in specified format.
     */
    protected function _getCurrentTime($format = self::TIME_FORMAT): string
    {
        return date($format, DbHelper::getCurrentTimestamp(true));
    }

    /**
     * Returns timestamp when specified task last started (returns {@link SchedulerTaskProcess::should_be_started_at}).
     *
     * @param int    $taskId
     * @param string $format
     *
     * @return null|string Time in specified format or =null if task not performed earlier.
     */
    protected function _getLastStartTime($taskId, $format = self::TIME_FORMAT): ?string
    {
        $lastPerformedAt = SchedulerTaskProcess::find()
            ->byTask($taskId)
            ->select(DbHelper::exprMax(SchedulerTaskProcess::ATTR_SHOULD_BE_STARTED_AT))
            ->scalar();

        if ($lastPerformedAt) {
            $time = strtotime($lastPerformedAt);

            if (is_int($time)) {
                return date($format, $time);
            }
        }

        return null;
    }

    /**
     *
     * @param string $timeInCronFormat
     *
     * @return CronExpression
     */
    protected function _prepareCronExpression($timeInCronFormat): CronExpression
    {
        return CronExpression::factory($timeInCronFormat);
    }

    /**
     * Compares specified datetime values (skips seconds at comparison).
     *
     * @param string|int $time1            Time in timestamp or string representation.
     * @param string|int $time2            Time in timestamp or string representation.
     * @param string     $comparisonFormat Format that used for time comparison.
     *
     * @return bool
     */
    protected function _isEqualTime($time1, $time2, $comparisonFormat = self::TIME_FORMAT): bool
    {
        return
            $this->_normalizeTime($time1, $comparisonFormat) ===
            $this->_normalizeTime($time2, $comparisonFormat);
    }

    /**
     * Normalizes specified time.
     *
     * @param string|int $time Time in timestamp or string representation.
     * @param string     $format
     *
     * @return false|string
     */
    protected function _normalizeTime($time, $format = self::TIME_FORMAT)
    {
        return TimeHelper::normalizeDatetime($time, $format);
    }

    /**
     * Prepares DateTime from specified string (using UTC timezone).
     *
     * @param int $timestamp
     *
     * @return \DateTime
     */
    protected function _prepareDateTimeFromTimestamp($timestamp): \DateTime
    {
        $timeZone = new \DateTimeZone('UTC');
        $date     = new \DateTime(DbHelper::formatDatetime($timestamp));

        $date->setTimezone($timeZone);

        return $date;
    }

    /**
     * Prepares specified message for the log.
     * Too long message will truncated by $limit.
     *
     * @param string $msg
     *
     * @param int    $limit
     *
     * @return string
     */
    protected function _prepareLogMsg($msg, $limit = 7500): string
    {
        $msg = StringHelper::truncate($msg, $limit);

        return PHP_EOL . '[' . DbHelper::formatDatetime(DbHelper::getCurrentTimestamp(true)) . '] ' . $msg . PHP_EOL . PHP_EOL;
    }

}