<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Segment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="segment-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-lg-6">
            <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 col-lg-3">
            <?= $form->field($model, 'type')->dropDownList($types) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= \yii\bootstrap4\Alert::widget([
                'body' => "نوع: فعلا بلااستفاده (در آینده جهت ایجاد صندوق های مرکزی جهت تبادلات مالی میان صندوق های اصلی و فرعی) و جداسازی مشتریان حقیقی و حقوقی.
                            صندوق های مرکزی درآینده دارای مشتریان حقوقی و همچنین مفهوم سرمایه گذاری خواهند بود.",
                'options' => array_merge([
                    'class' => 'alert alert-warning',
                ]),
            ]);?>
        </div>
    </div>
    <div class="form-group text-left">
        <?= Html::submitButton('ذخیره', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>