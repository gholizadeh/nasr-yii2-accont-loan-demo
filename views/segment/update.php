<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Segment */

$this->title = 'ویرایش صندوق: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'صندوق ها', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->seg_id, 'url' => ['view', 'id' => $model->seg_id]];
$this->params['breadcrumbs'][] = 'ویرایش';
?>
<div class="segment-update">
	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'types' => $types
    ]) ?>

</div>
