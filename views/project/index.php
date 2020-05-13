<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h2><?= Html::encode($this->title) ?> &nbsp; <?= Html::a('Create Project', ['create'], ['class' => 'btn btn-success']) ?></h2>

    <?= $this->render('_search', ['model' => $searchModel]) ?>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' =>  '_project_item'
    ]) ?>

</div>
