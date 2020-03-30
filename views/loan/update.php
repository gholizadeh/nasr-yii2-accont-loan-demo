<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */

$this->title = 'ویرایش تسهیلات: ' . $model->description;
$this->params['breadcrumbs'][] = ['label' => 'تسهیلات', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'ویرایش';
?>
<div class="loan-update">
	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'statuses' => $statuses,
        'loanTypes' => $loanTypes,
        'accounts' => $accounts,
        'selected_costs' => $selected_costs,
        'costs' => $costs,
        'cost_amounts' => $cost_amounts,
    ]) ?>

</div>
