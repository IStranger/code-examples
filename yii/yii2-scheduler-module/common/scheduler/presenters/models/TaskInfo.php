<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\presenters\models;

/**
 * The model (DTO) contains info about task and last execution process.
 */
class TaskInfo
{
    /**
     * @var \common\scheduler\models\SchedulerTaskProcess
     */
    public $lastProcess;

    /**
     * @var string CSS class for the last execution cell.
     */
    public $alertClass;

    /**
     * @var string
     */
    public $statusAlias;

    /**
     * @var string Datetime when started.
     */
    public $startedAt;

    /**
     * @var string Datetime when started (human readable format).
     */
    public $startedAtFormatted;
}