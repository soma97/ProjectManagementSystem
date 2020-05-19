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
         <h2><?= Html::encode("Efforts for ").Html::a($model->name, ['view', 'id' => $model->id]) ?></h2>
        </div>
        <div class="col-md-1">
            <br>
            <a target="_blank" href="/project/report?id=<?= $model->id ?>&target=efforts" class="btn btn-danger glyphicon glyphicon-open-file pull-right">PDF</a>
        </div>
    </div>
    <hr>
    <?php
        $activities = $model->getActivities()->all();

        foreach ($activities as $activity) {
            $efforts = $activity->getEfforts()->orderBy('updated_at DESC')->all();
            foreach ($efforts as $effort){
                echo "<small class='pull-right' style='color: #999999'>".Yii::$app->formatter->asDatetime($effort->updated_at, 'HH:mm dd.MM.yyyy'). "</small><h4>".
                    Html::encode($effort->getUser()->one()->username)." submitted $effort->hours hours on ". Html::a($activity->name, ['activity/view', 'id' => $activity->id]) .".</h4>".
                    "<small>".Html::encode($effort->description)."</small><hr>";
            }
        }
    ?>

</div>