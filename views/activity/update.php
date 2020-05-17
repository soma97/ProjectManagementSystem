<?php

use app\models\AddUserForm;
use app\models\Project;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Activity */

$this->title = 'Update Activity: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activity-update">

    <h1><?php
        echo Html::encode($model->name);
        echo '&nbsp';
        echo Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
        ]); ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-md-4 col-md-offset-2">
            <?php
            $currentProject = Project::findOne($model->project_id);
            $currentUser = User::findOne(Yii::$app->user->getId())->getUserHasProjects()->where(['project_id' => $model->project_id])->one();
            $usersOnProject = $currentProject->getUsers()->with('userHasProjects')->all();
            if($currentUser['role'] === 'owner' || ( $currentUser['role'] === 'participant' && $currentUser['internal'] == true)){
                $form = ActiveForm::begin();
                $userForm = new AddUserForm();

                echo $form->field($userForm, 'user')
                    ->dropDownList(
                        ArrayHelper::map($usersOnProject,'id','username'),           // Flat array ('id'=>'label')
                        ['prompt'=>'Select user']    // options
                    );

                echo $form->field($userForm, 'role')->textInput();

                echo Html::submitButton('Assign', ['class' => 'btn btn-primary', 'name' => 'assign-button']);
                ActiveForm::end();
            }
            ?>
        </div>
    </div>

</div>
