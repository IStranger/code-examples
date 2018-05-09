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

<div class="task-info-cron">

    <b><?= $model->cron_time ?></b>

    <?php if (is_array($cronPreviousRunDates)): ?>

        <div class="small">
            <?= Yii::t('app', 'previous at') ?>:
            <?= Html::ul($cronPreviousRunDates) ?>
        </div>

    <?php endif; ?>

    <?php if (is_array($cronNextRunDates)): ?>

        <div class="small">
            <?= Yii::t('app', 'next at') ?>:
            <?= Html::ul($cronNextRunDates) ?>
        </div>

    <?php endif; ?>

</div>
