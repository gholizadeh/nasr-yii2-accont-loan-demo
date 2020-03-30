<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = 'ویرایش حساب: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'حساب ها', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'ویرایش';
?>
<div class="account-update">
	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'statuses' => $statuses,
        'accountTypes' => $accountTypes
    ]) ?>

</div>
