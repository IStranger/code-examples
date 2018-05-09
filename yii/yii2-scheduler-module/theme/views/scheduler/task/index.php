<?php

use common\helpers\Html;

/**
 * @var yii\web\View                                          $this
 * @var yii\data\ActiveDataProvider                           $dataProvider
 * @var common\scheduler\models\search\SchedulerTaskSearch    $searchModel
 * @var \common\scheduler\presenters\models\LastExecutionInfo $lastExecInfo
 * @var \Closure                                              $taskInfoFormatter
 */

$this->title                   = Yii::t('app', 'Scheduler Tasks');
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="scheduler-task-index">

    <div class="panel mini-box">
        <span class="box-icon bg-info">
            <i class="fa fa-list"></i>
        </span>
        <div class="box-info">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>


    <div class="well">
        <?= $this->render('_last-scheduled', ['lastExecInfo' => $lastExecInfo]); ?>
    </div>


    <?= $this->render('_task-grid', [
        'dataProvider'      => $dataProvider,
        'searchModel'       => $searchModel,
        'taskInfoFormatter' => $taskInfoFormatter,
    ]); ?>

</div>
