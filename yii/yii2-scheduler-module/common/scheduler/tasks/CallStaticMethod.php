<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\tasks;

/**
 * The task calls method of the specified class.
 */
class CallStaticMethod extends \common\scheduler\tasks\AbstractSchedulerTask
{
    /**
     * @var string Full class name.
     */
    public $class;

    /**
     * @var string Method name.
     */
    public $method;

    /**
     * @var array Arguments for method.
     */
    public $args = [];

    /**
     * Executes task.
     * Optionally may return some result (it will be logged).
     *
     * @return mixed
     */
    public function run()
    {
        return call_user_func_array([$this->class, $this->method], $this->args);
    }
}