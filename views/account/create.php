<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = 'افزودن حساب'. $this->context->getSegmentLable();
$this->params['breadcrumbs'][] = ['label' => 'حساب ها', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-create">

	<div class="row headline4">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'statuses' => $statuses,
        'accountTypes' => $accountTypes,
    ]) ?>

</div>
