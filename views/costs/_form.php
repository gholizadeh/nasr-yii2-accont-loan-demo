<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Cost */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cost-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-lg-9">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'status')->dropDownList($statuses) ?>
        </div>
    </div>
    <div class="form-group text-left">
        <?= Html::submitButton('ذخیره', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>