<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\interfaces;

/**
 * Interface ISchedulerTask describes scheduler task.
 */
interface ISchedulerTask
{
    /**
     * Executes task.
     * Optionally may return some result (it will be logged).
     *
     * @return mixed
     */
    public function run();
}