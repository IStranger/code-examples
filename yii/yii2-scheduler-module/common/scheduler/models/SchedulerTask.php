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
use common\behaviors\AttributeConvertBehavior;
use common\scheduler\models\queries\SchedulerTaskProcessQuery;
use common\scheduler\models\queries\SchedulerTaskQuery;

/**
 * The ActiveRecord class for table "{{%mc_scheduler_task}}".
 *
 * @property integer                   $id
 * @property string                    $title
 * @property integer                   $is_active
 * @property integer                   $as_runner_script
 * @property string                    $cron_time
 * @property string                    $allowable_time_deviation
 * @property string                    $class
 * @property array                     $options
 * @property string                    $created_at
 * @property integer                   $created_by_user_id
 *
 * @property User                      $createdByUser
 * @property SchedulerTaskProcess[]    $schedulerTaskProcesses
 * @property SchedulerTaskProcess|null $lastSchedulerTaskProcess
 */
class SchedulerTask extends \common\components\ActiveRecord
{
    /**
     * Relation name: Last scheduled task process.
     *
     * @see $lastSchedulerTaskProcess
     * @see getLastSchedulerTaskProcess()
     */
    const REL_LAST_SCHEDULED_TASK_PROCESS = 'lastSchedulerTaskProcess';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%mc_scheduler_task}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class'      => AttributeConvertBehavior::class,
                'attributes' => ['options'],
            ],
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
            [['title', 'is_active', 'as_runner_script', 'class', 'allowable_time_deviation', 'cron_time'], self::VALIDATOR_REQUIRED],

            [['options'], self::VALIDATOR_STRING],
            [['is_active', 'as_runner_script'], self::VALIDATOR_BOOLEAN],
            [['created_by_user_id'], self::VALIDATOR_INTEGER],
            [['title', 'class'], self::VALIDATOR_STRING, 'max' => 255],
            [['cron_time'], self::VALIDATOR_STRING, 'max' => 64],
            [['created_at'], self::VALIDATOR_SAFE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => Yii::t('app', 'ID'),
            'title'              => Yii::t('app', 'Title'),
            'is_active'          => Yii::t('app', 'Is active'),
            'as_runner_script'   => Yii::t('app', 'As runner script'),
            'cron_time'          => Yii::t('app', 'When execute (CRON format)'),
            'class'              => Yii::t('app', 'Class'),
            'options'            => Yii::t('app', 'Options'),
            'created_at'         => Yii::t('app', 'Created At'),
            'created_by_user_id' => Yii::t('app', 'Created By User ID'),
        ];
    }

    /**
     * @inheritdoc
     * @return SchedulerTaskQuery
     */
    public static function find()
    {
        return new SchedulerTaskQuery(static::class);
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
     * Relation: task processes.
     *
     * @return SchedulerTaskProcessQuery
     * @see $schedulerTaskProcesses
     */
    public function getSchedulerTaskProcesses(): SchedulerTaskProcessQuery
    {
        return $this->hasMany(SchedulerTaskProcess::class, ['task_id' => 'id']);
    }

    /**
     * Relation: last scheduled task process.
     *
     * @return SchedulerTaskProcessQuery
     * @see $lastSchedulerTaskProcess
     * @see SchedulerTask::REL_LAST_SCHEDULED_TASK_PROCESS
     */
    public function getLastSchedulerTaskProcess(): SchedulerTaskProcessQuery
    {
        $queryLastLogsProcessIds = SchedulerTaskProcess::find()
            ->onlyLastScheduledTaskProcesses()
            ->selectId();

        return $this
            ->hasOne(SchedulerTaskProcess::class, ['task_id' => 'id'])
            ->andOnCondition(['id' => $queryLastLogsProcessIds]);
    }
}
