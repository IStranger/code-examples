<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\presenters\models;

/**
 * The model (DTO) contains info about last scheduler execution.
 */
class LastExecutionInfo
{
    /**
     * @var int
     */
    public $timestamp;

    /**
     * @var string
     */
    public $at;

    /**
     * @var string
     */
    public $ago;

    /**
     * @var int
     */
    public $timeDiff;
}