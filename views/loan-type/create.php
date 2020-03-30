<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LoanType */

$this->title = 'افزودن نوع تسهیلات';
$this->params['breadcrumbs'][] = ['label' => 'انواع تسهیلات', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-type-create">

	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model
    ]) ?>

</div>
