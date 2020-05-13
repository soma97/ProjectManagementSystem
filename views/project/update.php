<?php

use app\models\AddUserForm;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Project */

$this->title = 'Update Project: ' . $model->name;
?>
<div class="project-update">

    <h1><?php
        echo Html::encode($model->name);
        echo '&nbsp';
        echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]);
        ?></h1>

    <div class="row">
        <div class="col-md-6">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-md-4 col-md-offset-2">
            <?php
                $currentUser = $model->getUserHasProjects()->where(['user_id' => Yii::$app->user->getId()])->one();
                $usersOnProject = $model->getUsers()->with('userHasProjects')->all();
                if($currentUser['role'] === 'owner'){
                    $form = ActiveForm::begin();
                    $userForm = new AddUserForm();
                    $listOfUsersToAdd = User::find()->all();
                    $listOfUsersToAdd = array_udiff($listOfUsersToAdd, $usersOnProject, function ($obj_a, $obj_b) {
                        return $obj_a->id - $obj_b->id;
                    });
                    $listItems = $listData=ArrayHelper::map($listOfUsersToAdd,'id','username');
                    echo $form->field($userForm, 'user')
                        ->dropDownList(
                        $listItems,           // Flat array ('id'=>'label')
                        ['prompt'=>'Select user']    // options
                        );

                    echo $form->field($userForm, 'role')
                        ->dropDownList(
                        ['participant' => 'Participant', 'supervisor' => 'Supervisor'],
                        ['prompt'=>'Select role', 'onchange' => 'if(this.value == "participant"){
                                $("#checkbox-id").removeAttr("disabled");
                            } else{
                                $("#checkbox-id").attr("disabled",true);
                            }']
                        );

                    echo $form->field($userForm, 'internal')->checkbox(['checked' => true, 'disabled' => true, 'id' => 'checkbox-id']);
                    echo Html::submitButton('Add user', ['class' => 'btn btn-primary', 'name' => 'add-button']);
                    ActiveForm::end();
                }
            ?>
        </div>
    </div>

</div>
