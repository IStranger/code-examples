<?php

use common\helpers\Html;

/**
 * @var yii\web\View                                               $this
 * @var common\scheduler\models\SchedulerTask                      $model
 * @var \backend\components\ActiveDataProvider                     $taskProcessProvider
 * @var \common\scheduler\models\queries\SchedulerTaskProcessQuery $taskProcessSearchModel
 * @var string[]|null                                              $cronPreviousRunDates
 * @var string[]|null                                              $cronNextRunDates
 */

$this->title                   = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Scheduler Tasks'), 'url' => ['task-index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="scheduler-task-view">

    <div class="panel mini-box">
        <span class="box-icon bg-info">
            <i class="fa fa-list"></i>
        </span>
        <div class="box-info">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>


    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data'  => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method'  => 'post',
            ],
        ]) ?>
    </p>


    <?= $this->render('_task-info', [
        'model'                => $model,
        'cronPreviousRunDates' => $cronPreviousRunDates,
        'cronNextRunDates'     => $cronNextRunDates,
    ]); ?>


    <hr/>


    <?= $this->render('_task-process-grid', [
        'model'                  => $model,
        'taskProcessProvider'    => $taskProcessProvider,
        'taskProcessSearchModel' => $taskProcessSearchModel,
    ]); ?>

</div>
