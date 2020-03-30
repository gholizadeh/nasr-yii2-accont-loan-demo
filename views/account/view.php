<?php

use app\models\LoanType;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Account */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'انواع حساب', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-view">

    <div class="headline4 text-left mb-2">
        <?= Html::a(Html::tag('i', '', ['class' => "fa fa-plus"]).' واریز', ['account/deposit', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Html::tag('i', '', ['class' => "fa fa-minus"]).' برداشت', ['account/withdraw', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('ویرایش', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('حذف', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'آیا مطمئن هستید?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="table-wrapper pt-3">
        <div class="row">
            <div class="col-12 col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'name',
                        [
                            'label' => 'نوع حساب',
                            'value' => isset($model->accountType) ? $model->accountType->name : '(خطا)',
                        ], 
                        [
                            'label' => 'افتتاح حساب',
                            'value' => isset($model->create_date) ? \yii::$app->pDate->JalaliDate($model->create_date) : '(خطا)',
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
                        [
                            'label' => 'صاحب حساب',
                            'value' => isset($model->client) ? $model->client->name : '(خطا)',
                        ], 
                        [
                            'label' => 'موجودی',
                            'value' => $model->remain_debit." تومان",
                        ],
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
                            'attribute' =>'type',
                            'filter'=> $trModel->getTypes(),
                            'value' => function($data, $row) use ($trModel){
                                return $trModel->getTypes()[$data->type];
                            }
                        ],
                        'description',
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
                            'attribute' => 'tr_user',
                            'value' => 'user.full_name'
                        ],
                        'reference_id',
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header'=>'برگشت', 
                            'headerOptions' => ['return' => '40'],
                            'template' => '{return}',
                            'buttons' => [
                                'return' => function ($url, $model, $key) {
                                    if($model->returned !== 1)
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
        <div class="row">
            <div class="col-12 my-2 ml-2" id="">
                آخرین تسهیلات
            </div>
            <div class="col-12">
            <?php Pjax::begin(); ?>
                <?= GridView::widget([
                    'id' => 'loan-grid',
                    'dataProvider' => $loanProvider,
                    'filterModel' => $loanModel, 
                    'columns' => [
                        [
                            'attribute' => 'type', 
                            'label' => 'نوع تسهیلات',
                            'filter' => LoanType::getLoanTypes(),
                            'value' => 'loanType.name'
                        ],
                        'description',
                        [
                            'attribute' => 'amount',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'remain',
                            'filter' => false,
                        ],
                        [
                            'attribute' => 'date',
                            'filter' => false,
                        ],
                        [
                            'attribute' =>'status',
                            'filter'=> $loanModel->getStatuses(),
                            'value' => function($data, $row) use ($loanModel){
                                return $loanModel->getStatuses()[$data->status];
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header'=>'نمایش', 
                            'headerOptions' => ['width' => '40'],
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::a(Html::tag('i', '', ['class' => "fa fa-eye"]), ['/loan/view', 'id' => $model->id], ['data-pjax' => 0]);
                                }
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
</div>
