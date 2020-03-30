<?php

use app\models\AccountType;
use app\models\LoanType;
use app\widgets\PageSize;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

    <div class="headline4 text-left mb-2">
        <?= Html::a(Html::tag('i', '', ['class' => "fa fa-plus"]).' اعطای تسهیلات', ['loan/create', 'id' => $model->client_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('ویرایش', ['update', 'id' => $model->client_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('حذف', ['delete', 'id' => $model->client_id], [
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
                        'client_id',
                        'name',
                        'cellphone'
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'remarks',
                        [
                            'label' => 'وضعیت',
                            'value' => $model->getStatuses()[$model->status],
                        ],
                        [
                            'label' => 'تاریخ عضویت در صندوق',
                            'value' => isset($model->date_added) ? \yii::$app->pDate->JalaliDate($model->date_added) : '(خطا)',
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
                لیست حساب ها
            </div>
            <div class="col-12">
            <?php Pjax::begin(); ?>
                <?= GridView::widget([
                    'id' => 'account-grid',
                    'dataProvider' => $accountProvider,
                    'filterModel' => $accountModel, 
                    'columns' => [
                        'id',
                        [
                            'attribute' => 'account_type', 
                            'label' => 'نوع',
                            'filter' => AccountType::getAccountTypes(),
                            'value' => 'accountType.name'
                        ],
                        'name',
                        [
                            'attribute' => 'remain_debit',
                            'filter' => false,
                        ],
                        [
                            'attribute' =>'status',
                            'filter'=> $accountModel->getStatuses(),
                            'value' => function($data, $row) use ($accountModel){
                                return $accountModel->getStatuses()[$data->status];
                            }
                        ],
                        [
                            'attribute' =>'create_date',
                            'value' =>  function($data, $row){
                                return isset($data->create_date) ? \yii::$app->pDate->JalaliDate($data->create_date) : '(خطا)';
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header'=>'نمایش', 
                            'headerOptions' => ['width' => '40'],
                            'template' => '{view}',
                            'buttons' => [
                                'view' => function ($url, $model, $key) {
                                    return Html::a(Html::tag('i', '', ['class' => "fa fa-eye"]), ['/account/view', 'id' => $model->id], ['data-pjax' => 0]);
                                }
                            ]
                        ],
                    ],
                    'tableOptions' =>['class' => 'table table-striped table-bordered table-sm table-hover'],
                    'filterRowOptions' => ['class' => 'sm-row']
                ]); ?>
            <?php Pjax::end(); ?>
            </div>
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
                        'id',
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
                            'value' =>  function($data, $row){
                                return isset($data->date) ? \yii::$app->pDate->JalaliDate($data->date) : '(خطا)';
                            }
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
