<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

/* @var $model app\models\Project */

$this->title = Html::encode($model->name);
\yii\web\YiiAsset::register($this);
?>
<div class="project-view">

    <div class="row">
        <div class="col-md-11">
            <h2><?= Html::encode("Activities for ").Html::a($model->name, ['view', 'id' => $model->id]) ?></h2>
        </div>
        <div class="col-md-1">
            <br>
            <a target="_blank" href="/project/report?id=<?= $model->id ?>&target=activities" class="btn btn-danger glyphicon glyphicon-open-file pull-right">PDF</a>
        </div>
    </div>
    <hr>

    <?php
        $activities = $model->getActivities()->all();

        foreach ($activities as $activity) {

            if($activity->parent_activity_id === null)
            {
                echo $this->render('_activity_item', [
                    'model' => $activity,
                    'indent' => 0
                ]);
                echo '<hr>';
            }
        }
    ?>

</div>