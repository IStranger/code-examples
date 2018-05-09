<?php

use yii\helpers\VarDumper;
use yii\widgets\DetailView;
use common\helpers\Html;
use common\helpers\ArrayHelper;

/**
 * @var yii\web\View                                 $this
 * @var common\scheduler\models\SchedulerTaskProcess $model
 */

$this->title                   = 'Task process log #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scheduler Tasks'), 'url' => ['task-index']];
$this->params['breadcrumbs'][] = ['label' => ArrayHelper::getValue($model->task, 'title'), 'url' => ['task-view', 'id' => $model->task_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scheduler-task-process-view">

    <div class="panel mini-box">
        <span class="box-icon bg-info">
            <i class="fa fa-list"></i>
        </span>
        <div class="box-info">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'id',
            'process_id',
            'task_id',
            'status',
            'should_be_started_at',
            'start_delay',
            'created_at',
            'updated_at',
            'created_by_user_id',
            [
                'attribute' => 'result',
                'format'    => 'html',
                'value'     => VarDumper::dumpAsString($model->result, 10, true),
            ],
            [
                'attribute' => 'log',
                'format'    => 'html',
                'value'     => '<pre>' . $model->log . '</pre>',
            ],
        ],
    ]) ?>

</div>
