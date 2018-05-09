<?php

use yii\helpers\VarDumper;
use yii\widgets\DetailView;
use common\helpers\Html;
use common\widgets\Pjax;
use backend\widgets\DynaGrid\DynaGrid;
use common\scheduler\models\SchedulerTaskProcess;

/**
 * @var yii\web\View                          $this
 * @var common\scheduler\models\SchedulerTask $model
 * @var string[]|null                         $cronPreviousRunDates
 * @var string[]|null                         $cronNextRunDates
 */


?>

<div class="task-info">

    <?php $this->beginBlock('cron') ?>

        <?= $this->render('_task-info-cron', [
            'model'                => $model,
            'cronPreviousRunDates' => $cronPreviousRunDates,
            'cronNextRunDates'     => $cronNextRunDates,
        ]); ?>

    <?php $this->endBlock(); ?>



    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'id',
            'title',
            'is_active',
            'as_runner_script',
            [
                'attribute' => 'cron_time',
                'format'    => 'html',
                'value'     => $this->blocks['cron'],
            ],
            'allowable_time_deviation',
            'class',
            [
                'attribute' => 'options',
                'format'    => 'html',
                'value'     => VarDumper::dumpAsString($model->options, 10, true),
            ],
            'created_at',
            'created_by_user_id',
        ],
    ]) ?>


</div>
