<?php

use app\models\AddUserForm;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Project */

$this->title = $model->name;
\yii\web\YiiAsset::register($this);
?>
<div class="project-view">

    <div class="row">
        <div class="col-md-3">
            <h2><?= Html::encode($model->name) ?></h2>
            <p> <?= Html::encode($model->description) ?> </p>
            <p>
                <?php
                $usersOnProject = $model->getUsers()->with('userHasProjects')->all();
                $currentUser = $model->getUserHasProjects()->where(['user_id' => Yii::$app->user->getId()])->one();
                if($currentUser['role'] === 'owner'){
                    echo Html::a('Settings', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
                    echo '&nbsp';
                }
                if($currentUser['role'] === 'owner' || ($currentUser['role'] === 'participant' && $currentUser['internal'] == true)) {
                    echo '&nbsp';
                    echo Html::a('Create activity', ['activity/create', 'project_id' => $model->id], ['class' => 'btn btn-success']);
                }
                echo '<br><br><br><h4>Project members</h4><hr>';
                foreach ($usersOnProject as $userOnProject){
                    echo "<div class='well well-sm' style='background-color: #555555'>".$userOnProject['username'].' ('.$userOnProject['userHasProjects'][0]['role']. ($userOnProject['userHasProjects'][0]['internal']==false ? ' - external)':')').
                        (($currentUser['role']==='owner' && $currentUser['user_id'] != $userOnProject['id']) ? "<span class='pull-right'><a href='/project/removeuser?userId=".$userOnProject['id']."&id=$model->id' style='color:#bb1111;'><b>X</b></a></span>" : "")."</div>";
                }
                ?>
            </p>
        </div>
        <div class="col-md-9">
   <?php foreach ($model->getActivitiesFor(null)->all() as $row){ ?>
       <hr>
       <h3> <?= Html::a($row['name'], ['activity/view', 'id' => $row['id']]) ?></h3>
       <p><?= Yii::$app->formatter->asPercent($model->getCompletionForActivity($row['id'])/100,2) ?> completed</p>
    <?php }?>
        </div>
    </div>

</div>
