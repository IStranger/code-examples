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

/**
 * The ActiveRecord class for table "{{%mc_scheduler_process}}".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $type
 * @property string  $log
 * @property string  $created_at
 * @property string  $updated_at
 * @property integer $created_by_user_id
 *
 * @property User    $createdByUser
 */
class SchedulerProcess extends \common\components\ActiveRecord
{
    const ATTR_CREATED_AT = 'created_at';

    const STATUS_DRAFT   = 1;
    const STATUS_PROCESS = 3;
    const STATUS_SUCCESS = 5;
    const STATUS_ERROR   = 7;

    const TYPE_REGULAR = 1;
    const TYPE_MANUAL  = 3;

    /**
     * Statuses (array keys) and it aliases (array values).
     * @return array
     */
    public static function statusAll(): array
    {
        return [
            self::STATUS_DRAFT   => Yii::t('app', 'Draft'),
            self::STATUS_PROCESS => Yii::t('app', 'Process'),
            self::STATUS_SUCCESS => Yii::t('app', 'Success'),
            self::STATUS_ERROR   => Yii::t('app', 'Error'),
        ];
    }

    /**
     * Types (array keys) and it aliases (array values).
     * @return array
     */
    public static function typeAll(): array
    {
        return [
            self::TYPE_REGULAR => Yii::t('app', 'Regular'),
            self::TYPE_MANUAL  => Yii::t('app', 'Manual'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%mc_scheduler_process}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(): array
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
    public function rules(): array
    {
        return [
            [['type', 'status'], self::VALIDATOR_REQUIRED],
            [['type', 'status', 'created_by_user_id'], self::VALIDATOR_INTEGER],
            [['log'], self::VALIDATOR_STRING],
            [['created_at', 'updated_at'], self::VALIDATOR_SAFE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id'                 => Yii::t('app', 'ID'),
            'status'             => Yii::t('app', 'Status'),
            'type'               => Yii::t('app', 'Type'),
            'log'                => Yii::t('app', 'Log'),
            'created_at'         => Yii::t('app', 'Created At'),
            'updated_at'         => Yii::t('app', 'Updated At'),
            'created_by_user_id' => Yii::t('app', 'Created By User ID'),
        ];
    }

    /**
     * @inheritdoc
     * @return SchedulerProcessQuery
     */
    public static function find(): SchedulerProcessQuery
    {
        return new SchedulerProcessQuery(static::class);
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
     * Returns status alias.
     *
     * @return string|null
     * @see statusAll()
     */
    public function getStatusAlias(): ?string
    {
        return ArrayHelper::getValue(self::statusAll(), $this->status);
    }

    /**
     * Returns type alias.
     *
     * @return string|null
     * @see typeAll()
     */
    public function getTypeAlias(): ?string
    {
        return ArrayHelper::getValue(self::typeAll(), $this->type);
    }
}
