<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\exceptions;

/**
 * The common scheduler exception.
 *
 * NOTE: Do not thrown it directly!
 */
abstract class AbstractSchedulerException extends \Exception implements ISchedulerException
{
}