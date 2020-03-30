<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AccountType */

$this->title = 'افزودن نوع حساب';
$this->params['breadcrumbs'][] = ['label' => 'انواع حساب', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-type-create">

	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
