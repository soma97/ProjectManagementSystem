<?php

use app\models\Effort;
use app\models\UserHasProject;
use yii\db\Query;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Activity */

$this->title = Html::encode($model->name);
\yii\web\YiiAsset::register($this);
?>
<div class="activity-view">

    <h3><?= $model->getParentActivity()->count() > 0 ? (Html::a($model->getParentActivity()->one()->name,
                ['view', 'id' => $model->parent_activity_id]) .' / '. $this->title)
            :(Html::a($model->getProject()->one()->name,
                    ['/project/view', 'id' => $model->project_id]).' / '. $this->title) ?></h3>

    <div>
        <?php
            $userProjectRelation = UserHasProject::findOne(['user_id' => Yii::$app->user->id, 'project_id' => $model->project_id]);
            $uiAdjust = true;
            if($userProjectRelation['role'] === 'owner' || ($userProjectRelation['role']==='participant' && $userProjectRelation['internal'] == true)) {
                $uiAdjust = false;
                echo Html::a('Settings', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
                echo '&nbsp';
                echo Html::a('Create subactivity', ['activity/create', 'project_id' => $model->project_id, 'parent_id' => $model->id], ['class' => 'btn btn-success']);
            }
            $usersOnActivity = (new Query())->select(['*'])->from('user')->innerJoin('user_has_activity', 'user_id=id')->where(['activity_id' => $model->id])->all();
            foreach ($usersOnActivity as $userOnActivity){
                echo "<span class='well well-sm pull-right' style='background-color: #555555; margin-left: 5px;'>".Html::encode($userOnActivity['username']).' ('.$userOnActivity['role'].")".
                    (($userProjectRelation['role']==='owner' || Yii::$app->user->id == $userOnActivity['id']) ? "&nbsp;<span class='pull-right'><a href='/activity/removeuser?userId=".$userOnActivity['id']."&id=$model->id' style='color:#bb1111;'><b>X</b></a></span>" : "")."</span>";
            }
            if(sizeof($usersOnActivity) == 0){
                echo "<span class='well well-sm pull-right' style='background-color: #555555;'>There are no users assigned to this activity</span>";
            }
            echo $uiAdjust ? "<br>" : "";
        ?>
    </div>
    <h4>Status:
        <?php
            if(($completionPercentage = $model->getCompletionPercentage()) < 100 && strtotime($model->done_until) < strtotime("now")){
                echo "<span class='text-danger'>Overdue</span>";
            }
            else {
                echo $completionPercentage < 100 ? "In progress" : "Done";
            }
        ?></h4>
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
            [
                'attribute' => 'done_until',
                'format'    => ['datetime', 'HH:mm dd.MM.yyyy']
            ]
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
            if ($completionPercentage < 100 && \app\models\UserHasActivity::findOne(['user_id' => $currentUser['user_id'], 'activity_id' => $model->id]) != null) {
                $form = ActiveForm::begin();
                $effortModel = new Effort();

                echo $form->field($effortModel, 'description')->textInput();

                echo $form->field($effortModel, 'hours')
                    ->dropDownList(
                        ['1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','11'=>'11','12'=>'12'],
                        ['prompt'=>'Select hours']
                    );

                echo Html::submitButton('Add effort', ['class' => 'btn btn-primary', 'name' => 'add-button']);
                ActiveForm::end();
            }
            echo '</div><div class="col-md-8">';
            foreach ($model->getEfforts()->orderBy('updated_at DESC')->all() as $effort) {
                $username = $effort->user->username;
                echo "<hr><small class='pull-right' style='color: #999999'>".Yii::$app->formatter->asDatetime($effort->updated_at, 'HH:mm dd.MM.yyyy')."</small><h4>".Html::encode($username)." submitted $effort->hours hours.</h4>"."<small>".Html::encode($effort->description)."</small>";
            }
            echo '</div></div>';
        }
        else {
            foreach ($subActivities->all() as $subactivity){
     ?>
                <hr>
                <h3><?= Html::a($subactivity['name'], ['activity/view', 'id' => $subactivity['id']]) ?></h3>
                <p><?= Yii::$app->formatter->asPercent($subactivity->getCompletionPercentage()/100,2) ?> completed</p>
    <?php   }
        } ?>
</div>
