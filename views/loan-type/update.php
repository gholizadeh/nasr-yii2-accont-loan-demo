<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LoanType */

$this->title = 'ویرایش نوع تسهیلات: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'انواع تسهیلات', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'ویرایش';
?>
<div class="loan-type-update">
	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
