<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Project */
/* @var $role string */

$this->title = $model->name;
\yii\web\YiiAsset::register($this);
?>
<div class="project-view">

    <div class="row">
        <div class="col-md-11">
            <h2><?= Html::encode("Income and outcome for ").Html::a($model->name, ['view', 'id' => $model->id]) ?></h2>
        </div>
        <div class="col-md-1">
            <br>
            <a target="_blank" href="/project/report?id=<?= $model->id ?>&target=revenue" class="btn btn-danger glyphicon glyphicon-open-file pull-right">PDF</a>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-md-3">
            <?php
            if($role === 'owner')
            {
                $form = ActiveForm::begin();
                $postModel = new \app\models\Revenue();
            ?>

            <?= $form->field($postModel, 'type')->dropDownList(
                ['Income'=>'Income', 'Outcome' => 'Outcome'],
                ['prompt'=>'Select type']
            ); ?>

            <?= $form->field($postModel, 'description')->textInput() ?>

            <?= $form->field($postModel, 'amount')->textInput() ?>

            <?php
                echo Html::submitButton('Submit', ['class' => 'btn btn-primary']);
                ActiveForm::end();
            }
            ?>
        </div>

        <div class="col-md-9">
            <?php
                $revenue = 0;
                $incomesOutcomes = $model->getRevenues()->orderBy('updated_at ASC')->all();
                foreach ($incomesOutcomes as $oneItem){
                    echo "<small class='pull-right' style='color: #999999'>".Yii::$app->formatter->asDatetime($oneItem->updated_at, 'HH:mm dd.MM.yyyy')."</small><h4>$oneItem->type: $oneItem->amount €</h4>"."<small>".Html::encode($oneItem->description)."</small><hr>";
                    $oneItem->type === 'Income' ? $revenue += $oneItem->amount : $revenue -= $oneItem->amount;
                }
                echo "<h3>Profit: $revenue €</h3>"
            ?>
        </div>
    </div>

</div>