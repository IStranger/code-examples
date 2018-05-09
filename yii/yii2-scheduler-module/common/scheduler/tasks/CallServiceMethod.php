<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\tasks;

use Yii;

/**
 * The task calls method of of the specified service.
 *
 * NOTE: The services will be resolved via main app service locator: Yii::$app.
 */
class CallServiceMethod extends \common\scheduler\tasks\AbstractSchedulerTask
{
    /**
     * @var string Application service name.
     */
    public $service;

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
        $service = Yii::$app->get($this->service);

        return call_user_func_array([$service, $this->method], $this->args);
    }
}