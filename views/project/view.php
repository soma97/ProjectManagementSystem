<?php

use app\models\AddUserForm;
use app\models\User;
use yii\bootstrap\Dropdown;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Menu;

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

                <?php
                $usersOnProject = (new Query())->select(['*'])->from('user_has_project')->innerJoin('user', 'user_id=id')->where(['project_id' => $model->id])->all();

                $currentUser = $model->getUserHasProjects()->where(['user_id' => Yii::$app->user->getId()])->one();
                if($currentUser['role'] === 'owner'){
                    echo Html::a('Settings', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']);
                    echo '&nbsp';
                }
                if($currentUser['role'] === 'owner' || ($currentUser['role'] === 'participant' && $currentUser['internal'] == true)) {
                    echo '&nbsp';
                    echo Html::a('Create activity', ['activity/create', 'project_id' => $model->id], ['class' => 'btn btn-primary']);
                }
                if($currentUser['role'] === 'owner' || $currentUser['role'] === 'supervisor'){
                    ?>
                    <br><br>
                    <div class="dropdown">
                    <button  data-toggle="dropdown" type="button" class="btn btn-danger dropdown-toggle">Reports <span class="caret"></span></button>
                    <?= Dropdown::widget([
                        'items' => [
                            ['label' => 'Income and outcome', 'url' => "/project/revenue?id=$model->id"],
                            ['label' => 'Activities', 'url' => "/project/activities?id=$model->id"],
                            ['label' => 'Efforts', 'url' => "/project/efforts?id=$model->id"],
                        ],
                    ]) ?>
                    </div>
            <?php
                }
                echo '<br><br><br><h4>Project members</h4><hr>';
                foreach ($usersOnProject as $userOnProject){
                    echo "<div class='well well-sm' style='background-color: #555555'>".Html::encode($userOnProject['username']).' ('.$userOnProject['role']. ($userOnProject['internal']==false ? ' - external)':')').
                        (($currentUser['role']==='owner' && $currentUser['user_id'] != $userOnProject['id']) ? "<span class='pull-right'><a href='/project/removeuser?userId=".$userOnProject['id']."&id=$model->id' class='glyphicon glyphicon-log-out' style='color:#870505;'></a></span>" : "")."</div>";
                }
                ?>
        </div>
        <div class="col-md-9">
   <?php foreach ($model->getActivitiesFor(null)->all() as $row){ ?>
       <hr>
       <h3><?= Html::a($row['name'], ['activity/view', 'id' => $row['id']]) ?></h3>
       <p><?= Yii::$app->formatter->asPercent($model->getCompletionForActivity($row['id'])/100,2) ?> completed</p>
    <?php }?>
        </div>
    </div>

</div>
