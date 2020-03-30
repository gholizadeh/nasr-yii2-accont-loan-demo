<?php

use app\assets\Digits2WordsAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AccountType */
/* @var $form yii\widgets\ActiveForm */

Digits2WordsAsset::register($this);
?>

<div class="account-type-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-lg-6">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= Html::label('شارژ ماهانه') ?>
            <?= Html::input('text', 'rate', $model->charge_rate, ['class' => 'form-control number']) ?>
            <?= $form->field($model, 'charge_rate')->hiddenInput(['id' => 'number-result'])->label(false) ?>
            <div id="result"></div>
        </div>
    </div>
    <div class="form-group text-left">
        <?= Html::submitButton('ذخیره', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>