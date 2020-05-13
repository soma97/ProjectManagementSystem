<?php
use yii\helpers\Html;
/** @var $model \app\models\Project */
?>

<div>
    <hr>
    <h3><?= Html::a(Html::encode($model->name), ['view', 'id' => $model->id]) ?></h3>
    <div>
        <?= Html::encode($model->description) ?>
    </div>
</div>
