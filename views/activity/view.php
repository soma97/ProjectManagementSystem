<?php

use app\models\Effort;
use app\models\UserHasProject;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Activity */

$this->title = $model->name;
\yii\web\YiiAsset::register($this);
?>
<div class="activity-view">

    <h3><?= $model->getParentActivity()->count() > 0 ? (Html::a($model->getParentActivity()->one()->name,
                ['view', 'id' => $model->parent_activity_id]) .' / '. Html::encode($this->title))
            :(Html::a($model->getProject()->one()->name,
                    ['/project/view', 'id' => $model->project_id]).' / '.$model->name) ?></h3>

    <p>
        <?php
        $userProjectRelation = UserHasProject::findOne(['user_id' => Yii::$app->user->id, 'project_id' => $model->project_id]);
        if($userProjectRelation['role'] === 'owner' || ($userProjectRelation['role']==='participant' && $userProjectRelation['internal'] == true)) {
            echo Html::a('Settings', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
            echo '&nbsp';
            echo Html::a('Create subactivity', ['activity/create', 'project_id' => $model->project_id, 'parent_id' => $model->id], ['class' => 'btn btn-success']);
        } ?>
    </p>
    <h4>Status: <?= ($completionPercentage = $model->getCompletionPercentage()) < 100 ? "In progress":"Done" ?></h4>
    <div class="progress">
        <div class="progress-bar <?= $completionPercentage >=100? "progress-bar-success": "active progress-bar-striped" ?>" role="progressbar" style="width: <?= $completionPercentage > 100 ? 100 : $completionPercentage ?>%" aria-valuenow="<?= $completionPercentage ?>" aria-valuemin="0" aria-valuemax="100"><?= Yii::$app->formatter->asPercent($completionPercentage/100,2) ?> complete</div>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'estimated_hours',
            'created_at:relativetime',
            'updated_at:relativetime',
        ],
        'options' => [
            'class' => 'table table-bordered detail-view',
        ]
    ]) ?>


    <?php
    $subActivities = $model->getActivities();
    if($subActivities->count() == 0) {
        echo '<div class="row"><div class="col-md-4">';
        $currentUser = UserHasProject::find()->where(['user_id' => Yii::$app->user->getId(), 'project_id' => $model->project_id])->one();
        if ($completionPercentage < 100 && ($currentUser['role'] === 'owner' || ($currentUser['role'] === 'participant' && $currentUser['internal'] == true))) {
            $form = ActiveForm::begin();
            $effortModel = new Effort();

            echo $form->field($effortModel, 'hours')->textInput();

            echo Html::submitButton('Add effort', ['class' => 'btn btn-primary', 'name' => 'add-button']);
            ActiveForm::end();
        }
        echo '</div><div class="col-md-8">';
        foreach ($model->getEfforts()->orderBy('updated_at DESC')->all() as $effort) {
            $username = $effort->user->username;
            echo "<hr><h4><small>".Yii::$app->formatter->asDatetime($effort->updated_at)."</small>  $username submitted $effort->hours hours.</h4>";
        }
        echo '</div></div>';
    }
    else{
     foreach ($subActivities->all() as $subactivity){ ?>
        <hr>
        <h3> <?= Html::a($subactivity['name'], ['activity/view', 'id' => $subactivity['id']]) ?></h3>
        <p><?=  Yii::$app->formatter->asPercent($subactivity->getCompletionPercentage()/100,2) ?> completed</p>
    <?php }
    } ?>
</div>
