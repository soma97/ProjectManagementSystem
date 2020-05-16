<?php

use kartik\datecontrol\DateControl;
use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Activity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="activity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'estimated_hours')->textInput() ?>

    <?= $form->field($model, 'done_until')->widget(
                    DateTimePicker::className(), [
                        'options' => ['placeholder' => 'Select time'],
                        'convertFormat' => true,
                        'pluginOptions' => [
                        'format' => 'yyyy-MM-dd HH:mm:ss',
                        'type'=> DateControl::FORMAT_DATETIME,
                        'todayHighlight' => true,
                    ]
        ]); ?>

    <?= $form->field($model, 'project_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'parent_activity_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
