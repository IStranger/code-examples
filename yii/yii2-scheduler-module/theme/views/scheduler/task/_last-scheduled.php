<?php

use common\helpers\Html;

/**
 * @var yii\web\View                                          $this
 * @var \common\scheduler\presenters\models\LastExecutionInfo $lastExecInfo
 */
?>

<div class="last-scheduled">

    <?php if (is_int($lastExecInfo->timestamp)): ?>


        <?= Yii::t('app', 'Last scheduled at <b>{at}</b> (UTC) [ {ago} ago ].', [
            'at'  => $lastExecInfo->at,
            'ago' => $lastExecInfo->ago,
        ]); ?>

        <?php if ($lastExecInfo->timeDiff > 120): ?>
            <br/>

            <?= Html::a('Start all scheduler tasks', '/scheduler/run', [
                'target'       => '_blank',
                'data-confirm' => Yii::t('app', 'Are you sure to start all tasks?'),
            ]); ?>

        <? endif; ?>


    <? else: ?>
        <?= Yii::t('app', 'Scheduler never worked.'); ?>
    <? endif; ?>

</div>