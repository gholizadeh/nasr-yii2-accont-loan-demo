<?php

use app\assets\Digits2WordsAsset;
use app\components\persianDate\DatePicker;
use app\models\AccountDetail;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

Digits2WordsAsset::register($this);

$this->title = 'پرداخت اقساط' . $this->context->getSegmentLable();
$this->params['breadcrumbs'][] = ['label' => 'تسهیلات', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-create">

	<div class="row headline4">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <div class="table-wrapper pt-3">
        <div class="row">
            <div class="col-12 col-md-6">
            <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label' => 'صاحب حساب',
                            'value' => isset($model->account->client) ? $model->account->client->name : '(خطا)',
                        ], 
                        [
                            'label' => 'حساب',
                            'value' =>  isset($model->account) ? $model->account->name : '(خطا)',
                        ],
                        [
                            'label' => 'تسویه تا تاریخ',
                            'value' => isset($model->account->settlement_til) ? \yii::$app->pDate->JalaliDate($model->account->settlement_til) : '(خطا)',
                        ], 
                        [
                            'label' => 'وضعیت',
                            'value' => $model->account->getStatuses()[$model->account->status],
                        ],
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'description',
                        'Installment_count',
                        'remain', 
                        [
                            'label' => 'وضعیت',
                            'value' => $model->getStatuses()[$model->status],
                        ],
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
        </div>

        <div class="account-form mt-2">
            <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <?= Html::label('تعداد قسط') ?>
                    <?php 
                        $installs = [];
                        for($i = 1; $i <= $model->remain; $i++){
                            $installs[$i] = $i;
                        }
                    ?>
		            <?= Html::dropDownList('install', '', $installs, ['class' => 'form-control', 'prompt'=>'']) ?>
                </div>
                <div class="col-md-6 col-lg-3">
                    <?= Html::label('معادل مبلغ (تعداد قسط را انتخاب کنید)') ?>
                    <?= Html::input('text', 'amount', $paymentModel->amount, ['class' => 'form-control number', 'disabled' => 'disabled']) ?>
                    <?= $form->field($paymentModel, 'amount', ['options' => ['class' => 'form-group mb-0']])->hiddenInput(['id' => 'number-result'])->label(false) ?>
                    <div id="result" class="mb-2 text-primary"></div>
                </div>
            </div>
            <div class="form-group text-left">
                <?= Html::submitButton('پرداخت', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <script>
                var install = <?= $install ?>;
                $(document).ready(function(){
                    $("select[name='install']").change(function(event){
                        $('.number').val($(this).val() * install).keyup();
                    });
                });
            </script>
        </div>
    </div>
</div>