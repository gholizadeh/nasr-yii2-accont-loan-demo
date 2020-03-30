<?php

use app\components\persianDate\DatePicker;
use app\models\Client;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Account */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="account-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'type')->dropDownList($accountTypes) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'account_client')->widget(AutoComplete::classname(), [
                'clientOptions' => [
                    'source' => Client::getSegmentClients(),
                    'selectFirst' => true,
                    'change' => new JsExpression("function (event, ui) {
                        if (ui.item == null){
                            $(this).val((ui.item ? ui.item.id : ''));
                            $('#account-client_id').val('');
                        }
                    }"),
                    'select' => new JsExpression("function( event, ui ) {
                        $('#account-client_id').val(ui.item.id);
                    }")
                ],
                'options' => [
                    'class' => 'form-control'
                ]
            ]) ?>
            <?= $form->field($model, 'client_id')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'status')->dropDownList($statuses) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= Html::label('تسویه شارژ تا تاریخ') ?>
            <?= DatePicker::widget([
                'name' => 'datepicker', 'value' => !is_null($model->settlement_til) ?  \yii::$app->pDate->JalaliDate($model->settlement_til) : \yii::$app->pDate->JalaliDate(),
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
                            $("#setlement-date").val(m.locale("en").format("YYYY-MM-DD"));
                        }
                    }'
                ]
            ]) ?>
            <?= $form->field($model, 'settlement_til')->hiddenInput(['id' => 'setlement-date'])->label(false) ?>
        </div>
    </div>
    <div class="form-group text-left">
        <?= Html::submitButton('ذخیره', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>