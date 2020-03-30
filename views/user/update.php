<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'ویرایش کاربر: ' . $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'کاربران', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->user_id, 'url' => ['نمایش', 'id' => $model->user_id]];
$this->params['breadcrumbs'][] = 'ویرایش';
?>
<div class="user-update">
	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'roles' => $roles,
        'segments' => $segments,
        'statuses' => $statuses,
        'socialNames' => $socialNames
    ]) ?>

</div>
