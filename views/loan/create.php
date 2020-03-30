<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */

$this->title = 'افزودن تسهیلات'. $this->context->getSegmentLable();
$this->params['breadcrumbs'][] = ['label' => 'تسهیلات', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-create">

	<div class="row headline4">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'statuses' => $statuses,
        'loanTypes' => $loanTypes,
        'accounts' => $accounts,
        'selected_costs' => [],
        'costs' => $costs,
        'cost_amounts' => $cost_amounts,
    ]) ?>

</div>
