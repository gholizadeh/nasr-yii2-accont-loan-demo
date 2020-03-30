<?php

use app\models\LoanPayment;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Loan */

$this->title = $model->description;
$this->params['breadcrumbs'][] = ['label' => 'تسهیلات', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-view">

    <div class="row headline4">
        <div class="col-6 mt-1">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-6 text-left pr-0 mt-1">
            <?php if($model->remain != 0){ ?>
                <?= Html::a(Html::tag('i', '', ['class' => "fa fa-plus"]).' واریز قسط', ['loan/deposit', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <?php } ?>
            <?php if($model->remain == $model->Installment_count){ ?>
                <?= Html::a('ویرایش', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('حذف', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'آیا مطمئن هستید?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php } ?>
        </div>
    </div>
    <div class="table-wrapper pt-3">
        <div class="row">
            <div class="col-12 col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'description',
                        [
                            'label' => 'نوع تسهیلات',
                            'value' => isset($model->loanType) ? $model->loanType->name : '(خطا)',
                        ], 
                        [
                            'label' => 'تاریخ اعطا',
                            'value' => isset($model->date) ? \yii::$app->pDate->JalaliDate($model->date) : '(خطا)',
                        ], 
                        [
                            'label' => 'کاربر اعطا کننده',
                            'value' => isset($model->user) ? $model->user->full_name : '(خطا)',
                        ], 
                        [
                            'label' => 'آخرین ویرایش',
                            'value' => isset($model->modified_date) ? \yii::$app->pDate->JalaliDateTime($model->modified_date) : '(خطا)',
                        ], 
                        [
                            'label' => 'آخرین ویرایشگر',
                            'value' => isset($model->lastModifiedBy) ? $model->lastModifiedBy->full_name : '(خطا)',
                        ], 
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'amount',
                        [
                            'label' => 'حساب',
                            'value' => isset($model->account) ? $model->account->name : '(خطا)',
                        ], 
                        [
                            'label' => 'صاحب حساب',
                            'value' => isset($model->account->client) ? $model->account->client->name : '(خطا)',
                        ], 
                        [
                            'label' => 'شروع اقساط',
                            'value' => isset($model->first_installment) ? \yii::$app->pDate->JalaliDate($model->first_installment) : '(خطا)',
                        ],
                        'Installment_count',
                        [
                            'label' => 'اقساط باقی',
                            'value' => $model->remain." قسط",
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
    </div>
    <div class="table-wrapper pt-3">
    <div class="row">
        <div class="col-12 my-2 ml-2" id="">
            آخرین تراکنش ها
        </div>
        <div class="col-12">
            <?php Pjax::begin(); ?>
                <?= GridView::widget([
                    'id' => 'transaction-grid',
                    'dataProvider' => $trProvider,
                    'filterModel' => $trModel, 
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'amount',
                            'filter' => false,
                        ],
                        [
                            'attribute' =>'date',
                            'value' =>  function($data, $row){
                                return isset($data->date) ? \yii::$app->pDate->JalaliDateTime($data->date) : '(خطا)';
                            }
                        ],
                        [
                            'attribute' =>'modified_date',
                            'value' =>  function($data, $row){
                                return isset($data->modified_date) ? \yii::$app->pDate->JalaliDateTime($data->modified_date) : '(خطا)';
                            }
                        ],
                        [
                            'attribute' => 'user',
                            'value' => 'lastModifiedBy.full_name'
                        ],
                        [
                            'attribute' =>'status',
                            'filter'=> $trModel->getStatuses(),
                            'value' => function($data, $row) use ($trModel){
                                return $trModel->getStatuses()[$data->status];
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header'=>'رد پرداخت', 
                            'headerOptions' => ['return' => '40'],
                            'template' => '{return}',
                            'buttons' => [
                                'return' => function ($url, $model, $key) {
                                    if($model->status !== LoanPayment::STAT_DENIED)
                                        return Html::a(Html::tag('i', '', ['class' => "fa fa-sync"]), ['return', 'id' => $model->id], [
                                            'data' => [
                                                'confirm' => 'آیا مطمئن هستید?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                },
                            ]
                        ],
                    ],
                    'tableOptions' =>['class' => 'table table-striped table-bordered table-sm table-hover'],
                    'filterRowOptions' => ['class' => 'sm-row']
                ]); ?>
            <?php Pjax::end(); ?>
            </div>
        </div>
</div>
