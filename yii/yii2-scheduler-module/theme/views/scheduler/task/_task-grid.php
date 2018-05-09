<?php

use common\widgets\Pjax;
use common\helpers\Html;
use common\scheduler\models\SchedulerTask;
use backend\widgets\DynaGrid\DynaGrid;

/**
 * @var yii\web\View                                       $this
 * @var yii\data\ActiveDataProvider                        $dataProvider
 * @var common\scheduler\models\search\SchedulerTaskSearch $searchModel
 * @var \Closure                                           $taskInfoFormatter
 */
?>


<?php
Pjax::begin(['id' => 'pjaxContainer']);

// NOTE: pjax container should wrap all columns (with filter widgets like Select2)
//       to correct capture of all necessary resources. Don't use built-in PJax in GridView.


// ---------------------------------------------------------
// ------ Prepare grid columns

$gridHeading = '<h3 class="panel-title">' . Html::icon('th-list') . ' ' . Html::encode($this->title) . ' </h3>';
$gridColumns = [
    // ['class' => 'yii\grid\SerialColumn'],
    'id',
    'title',
    'is_active',
    'as_runner_script',
    'cron_time',
    [
        'label'  => 'Last started at',
        'format' => 'html',
        'value'  => function (SchedulerTask $model) use ($taskInfoFormatter) {

            /**
             * @var \common\scheduler\presenters\models\TaskInfo $taskInfo
             */
            $taskInfo = $taskInfoFormatter($model);

            if ($taskInfo->lastProcess) {

                $html = "{$taskInfo->startedAt} ({$taskInfo->statusAlias})";
                $html .= '<br/>';
                $html .= Html::tag('small', $taskInfo->startedAtFormatted);

                return Html::div($html, [
                    'class' => 'alert alert-' . $taskInfo->alertClass,
                    'style' => 'margin: 0; padding: 5px 10px;',
                ]);
            }

            return Html::span('(never)', ['class' => 'not-set']);
        },
    ],

    [
        'class'    => 'yii\grid\ActionColumn',
        'template' => '{view} {start}',
        'buttons'  => [
            'view'  => function ($url, SchedulerTask $model) {
                return Html::iconLink('eye', ['task-view', 'id' => $model->id], '', [
                    'data-pjax' => 0,
                    'title'     => Yii::t('app', 'View task details and logs'),
                ]);
            },
            'start' => function ($url, SchedulerTask $model) {
                return Html::iconLink('star', ['task-start', 'id' => $model->id], '', [
                    'data-pjax'    => 0,
                    'data-method'  => 'post',
                    'data-confirm' => Yii::t('app', 'Are you sure you want to start "{name}" task right now?', ['name' => $model->title]),
                    'title'        => Yii::t('app', 'Start task right now'),
                ]);
            },
        ],
    ],
];


// ---------------------------------------------------------
// ------ Render Grid

echo DynaGrid::widget([
    'columns'     => $gridColumns,
    'options'     => ['id' => 'scheduler-task-grid'],
    'gridOptions' => [
        'pjax'         => false,
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'panel'        => ['heading' => $gridHeading],
        'toolbar'      => [
            [
                'content' =>
                    // $this->blocks['gridButtons'] . ' ' .
                    Html::iconLink('repeat', [''], '', [
                        'data-pjax' => 0,
                        'class'     => 'btn btn-default',
                        'title'     => Yii::t('app', 'Reset Grid'),
                        'onclick'   => 'location.reload()',
                    ]),
            ],
            ['content' => '{dynagridFilter}{dynagridSort}{dynagrid}'],
            '{export}',
        ],
    ],
]);

Pjax::end();
?>