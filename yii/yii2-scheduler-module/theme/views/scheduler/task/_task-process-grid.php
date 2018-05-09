<?php

use common\helpers\Html;
use common\widgets\Pjax;
use common\scheduler\models\SchedulerTaskProcess;
use backend\widgets\DynaGrid\DynaGrid;

/**
 * @var yii\web\View                                               $this
 * @var common\scheduler\models\SchedulerTask                      $model
 * @var \backend\components\ActiveDataProvider                     $taskProcessProvider
 * @var \common\scheduler\models\queries\SchedulerTaskProcessQuery $taskProcessSearchModel
 */

?>


<div class="task-process-grid">

    <?php
    Pjax::begin(['id' => 'pjaxContainer']);
    // NOTE: pjax container should wrap all columns (with filter widgets like Select2)
    //       to correct capture of all necessary resources. Don't use built-in PJax in GridView.


    // ---------------------------------------------------------
    // ------ Prepare grid columns

    $gridHeading = '<h3 class="panel-title">' . Html::icon('th-list') . ' ' . Yii::t('app', 'Task process log') . ' </h3>';
    $gridColumns = [
        'created_at',
        'id',
        'process_id',
        [
            'attribute' => 'status',
            'filter'    => Html::activeDropDownListUsingSelect2($taskProcessSearchModel, 'status', SchedulerTaskProcess::statusAll()),
            'value'     => function (SchedulerTaskProcess $model) {
                return $model->getStatusAlias();
            },
        ],
        // 'task_id',
        // 'result',
        // 'log',
        'should_be_started_at',
        'start_delay',

        'updated_at',
        'created_by_user_id',
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons'  => [
                'view' => function ($url, SchedulerTaskProcess $model) {
                    return Html::iconLink('eye', ['task-process-view', 'id' => $model->id]);
                },
            ],
        ],
    ];


    // ---------------------------------------------------------
    // ------ Render Grid

    echo DynaGrid::widget([
        'columns'     => $gridColumns,
        'options'     => ['id' => 'scheduler-task-process-grid'],
        'gridOptions' => [
            'pjax'         => false,
            'dataProvider' => $taskProcessProvider,
            'filterModel'  => $taskProcessSearchModel,
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

</div>
