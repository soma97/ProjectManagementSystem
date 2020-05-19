<?php

use app\models\Effort;
use app\models\UserHasProject;
use yii\db\Query;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Activity */
/* @var $indent integer */

\yii\web\YiiAsset::register($this);
?>
<div class="activity-view">
    <br>
    <?php
        for($i=0;$i<$indent;++$i) {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $completionPercentage = $model->getCompletionPercentage();
    ?>

    <span class="glyphicon <?= $indent % 2 == 0 ? "glyphicon-circle-arrow-right" : "glyphicon-chevron-right" ?>"></span>&nbsp;<?= Html::a($model->name, ['/activity/view', 'id' => $model->id]) ?>
    <span><small style='color: #999999'>&nbsp; <?= Yii::$app->formatter->asPercent($completionPercentage/100,2) ?> complete</small></span>

    <?php
        $children = $model->getActivities()->all();

        foreach($children as $child) {
            echo $this->render('_activity_item', [
                'model' => $child,
                'indent' => $indent+1
            ]);
        }
    ?>
</div>

