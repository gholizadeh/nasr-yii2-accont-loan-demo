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

$this->title = (($type == AccountDetail::TYPE_DEPOSIT) ? 'واریز به' : 'برداشت از') . ' حساب' . $this->context->getSegmentLable();
$this->params['breadcrumbs'][] = ['label' => 'حساب ها', 'url' => ['index']];
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
                            'value' => isset($model->client) ? $model->client->name : '(خطا)',
                        ], 
                        [
                            'label' => 'موجودی',
                            'value' => $model->remain_debit." تومان",
                        ]
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label' => 'تسویه تا تاریخ',
                            'value' => isset($model->settlement_til) ? \yii::$app->pDate->JalaliDate($model->settlement_til) : '(خطا)',
                        ], 
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
                    <?= Html::label('مبلغ') ?>
                    <?= Html::input('text', 'amount', $detailModel->amount, ['class' => 'form-control number']) ?>
                    <?= $form->field($detailModel, 'amount', ['options' => ['class' => 'form-group mb-0']])->hiddenInput(['id' => 'number-result'])->label(false) ?>
                    <div id="result" class="mb-2 text-primary"></div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <?= $form->field($detailModel, 'description')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6 col-lg-3">
                    <?= $form->field($detailModel, 'reference_id')->textInput(['maxlength' => true, 'class' => 'form-control num']) ?>
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
                <?= Html::submitButton(($type == AccountDetail::TYPE_DEPOSIT) ? 'واریز' : 'برداشت', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>