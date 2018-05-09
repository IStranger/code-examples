<?php
/**
 * Created by [azamat/istranger]
 */

namespace common\scheduler\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use common\helpers\ArrayHelper;
use common\models\User;
use common\models\queries\UserQuery;
use common\scheduler\models\queries\SchedulerProcessQuery;
use common\scheduler\models\queries\SchedulerTaskQuery;
use common\scheduler\models\queries\SchedulerTaskProcessQuery;

/**
 * This is the model class for table "{{%mc_scheduler_task_process}}".
 *
 * @property integer          $id
 * @property integer          $process_id
 * @property integer          $task_id
 * @property integer          $status
 * @property string           $result
 * @property string           $log
 * @property string           $should_be_started_at
 * @property integer          $start_delay
 * @property string           $created_at
 * @property string           $updated_at
 * @property integer          $created_by_user_id
 *
 * @property User             $createdByUser
 * @property SchedulerTask    $task
 * @property SchedulerProcess $process
 */
class SchedulerTaskProcess extends \common\components\ActiveRecord
{
    const ATTR_SHOULD_BE_STARTED_AT = 'should_be_started_at';
    const ATTR_TASK_ID              = 'task_id';
    const ATTR_CREATED_AT           = 'created_at';

    const STATUS_DRAFT   = 1;
    const STATUS_PROCESS = 3;
    const STATUS_SUCCESS = 5;
    const STATUS_ERROR   = 7;

    /**
     * Statuses (array keys) and it aliases (array values).
     * @return array
     */
    public static function statusAll()
    {
        return [
            self::STATUS_DRAFT   => Yii::t('app', 'Draft'),
            self::STATUS_PROCESS => Yii::t('app', 'Process'),
            self::STATUS_SUCCESS => Yii::t('app', 'Success'),
            self::STATUS_ERROR   => Yii::t('app', 'Error'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mc_scheduler_task_process}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'              => BlameableBehavior::class,
                'createdByAttribute' => 'created_by_user_id',
                'updatedByAttribute' => null,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['process_id', 'task_id', 'status'], self::VALIDATOR_REQUIRED],
            [['process_id', 'task_id', 'status', 'start_delay', 'created_by_user_id'], self::VALIDATOR_INTEGER],
            [['log'], self::VALIDATOR_STRING],
            [['result', 'should_be_started_at', 'created_at', 'updated_at'], self::VALIDATOR_SAFE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                   => Yii::t('app', 'ID'),
            'process_id'           => Yii::t('app', 'Process ID'),
            'task_id'              => Yii::t('app', 'Task ID'),
            'status'               => Yii::t('app', 'Status'),
            'result'               => Yii::t('app', 'Result'),
            'log'                  => Yii::t('app', 'Log'),
            'start_delay'          => Yii::t('app', 'Start Delay'),
            'should_be_started_at' => Yii::t('app', 'Should Be Started At'),
            'created_at'           => Yii::t('app', 'Created At'),
            'updated_at'           => Yii::t('app', 'Updated At'),
            'created_by_user_id'   => Yii::t('app', 'Created By User ID'),
        ];
    }

    /**
     * @inheritdoc
     * @return SchedulerTaskProcessQuery
     */
    public static function find(): SchedulerTaskProcessQuery
    {
        return new SchedulerTaskProcessQuery(static::class);
    }

    /**
     * Relation: "created by" user.
     *
     * @return UserQuery
     * @see $createdByUser
     */
    public function getCreatedByUser(): UserQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by_user_id']);
    }

    /**
     * Relation: task.
     *
     * @return SchedulerTaskQuery
     */
    public function getTask(): SchedulerTaskQuery
    {
        return $this->hasOne(SchedulerTask::class, ['id' => 'task_id']);
    }

    /**
     * Relation: process.
     *
     * @return SchedulerProcessQuery
     */
    public function getProcess(): SchedulerProcessQuery
    {
        return $this->hasOne(SchedulerProcess::class, ['id' => 'process_id']);
    }

    /**
     * Returns status alias.
     * @return string|null
     *
     * @see statusAll()
     */
    public function getStatusAlias(): ?string
    {
        return ArrayHelper::getValue(self::statusAll(), $this->status);
    }
}
