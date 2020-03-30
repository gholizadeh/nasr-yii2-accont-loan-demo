<?php

use app\assets\Digits2WordsAsset;
use app\components\persianDate\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */
/* @var $form yii\widgets\ActiveForm */

Digits2WordsAsset::register($this);
$this->registerJsFile(
    '@web/js/loan-costs.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>

<div class="loan-form">
    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'loanForm'
         ]
    ]); ?>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <?= Html::label('مبلغ') ?>
            <?= Html::input('text', 'amount', $model->amount, ['class' => 'form-control number']) ?>
            <?= $form->field($model, 'amount', ['options' => ['class' => 'form-group mb-0']])->hiddenInput(['id' => 'number-result'])->label(false) ?>
            <div id="result" class="mb-2 text-primary"></div>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'loan_type')->dropDownList($loanTypes) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'account_id')->dropDownList($accounts) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'Installment_count')->textInput(['maxlength' => true, 'class' => 'form-control num']) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= Html::label('تاریخ اولین قسط') ?>
            <?= DatePicker::widget([
                'name' => 'datepicker', 'value' => \yii::$app->pDate->JalaliDate($model->first_installment),
                'options' => [
                    'class' => 'form-control',
                    'id' => 'jalaliInput'
                ],
                'clientOptions' => [
                    'formatDate' => 'YYYY/MM/DD',
                    'fontSize' => 14,
                    'cellHeight' => 25,
                    'cellWidth'  => 28,
                    'onSelect' => 'function(){
                        let m = moment.from($("#jalaliInput").val(), "fa", "YYYY/MM/DD");
                        if (m.isValid()){
                            $("#first-installment").val(m.locale("en").format("YYYY-MM-DD"));
                        }
                    }'
                ]
            ]) ?>
            <?= $form->field($model, 'first_installment')->hiddenInput(['id' => 'first-installment'])->label(false) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
        </div>
    </div>
    <div class="card">
        <div class="card-header">افزودن هزینه</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-lg-6">
                            <?= Html::label('هزینه', 'cost');?>
                            <?= Html::dropDownList('cost','', $costs, ['class' => 'form-control']);?>
                        </div>
                        <div class="col-lg-6">
                            <?= Html::label('تعداد', 'count');?>
                            <?= Html::input('text', 'count', '', ['class' => 'form-control num']); ?>
                        </div>
                        <div class="col-lg-6">
                            <?= Html::button('افزودن',array('class'=>'add-cost btn btn-success mt-4')); ?>
                            <div id="hidden-fields"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <legend>هزینه ها</legend>
                    <table class="table table-striped table-bordered table-sm table-hover">
                        <tbody id="selected-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
    </div>
    <div class="form-group text-left">
        <?= Html::submitButton('ذخیره', ['class' => 'btn btn-success mt-4']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <script type="text/javascript">
        var $selected_costs = <?= json_encode($selected_costs) ?>;
        var $costs = <?= json_encode($costs) ?>;
        var $cost_amounts = <?= json_encode($cost_amounts) ?>;
    </script>
</div>