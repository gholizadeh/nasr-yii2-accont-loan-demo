<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'فرم افزودن گروه کاربری';
$this->params['breadcrumbs'][] = ['label' => 'گروه های کاربری', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'statuses' => $statuses,
        'act_modules' => $act_modules,
        'all_modules' => $all_modules
    ]) ?>

</div>
