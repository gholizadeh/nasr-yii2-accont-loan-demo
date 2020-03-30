<?php

use app\models\LoanType;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\Loan */ 
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'تسهیلات'. $this->context->getSegmentLable();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="segment-index">
    <div class="row headline4">
        <div class="col-8 mt-1">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-4 text-left pr-0 mt-1">
            <?= Html::a(Html::tag('i', '', ['class' => "fa fa-sync"]).' به روزرسانی', ['update-status'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="table-wrapper table-responsive">
        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel, 
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'client',
                    'value' => 'account.client.name'
                ],
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
                    'filter'=> $searchModel->getStatuses(),
                    'value' => function($data, $row) use ($searchModel){
                        return $searchModel->getStatuses()[$data->status];
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header'=>'عملیات', 
                    'headerOptions' => ['width' => '80', 'class' => 'text-center'],
                    'contentOptions' => ['class' => 'text-center'],
                    'template' => '{view} {update} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a(Html::tag('i', '', ['class' => "fa fa-eye"]), $url, ['data-pjax' => 0]);
                        },
                        'update' => function ($url, $model, $key) {
                            if($model->remain == $model->Installment_count)
                                return Html::a(Html::tag('i', '', ['class' => "fa fa-edit"]), $url);
                        },
                        'delete' => function ($url, $model, $key) {
                            if($model->remain == $model->Installment_count)
                                return Html::a(Html::tag('i', '', ['class' => "fa fa-trash"]), ['delete', 'id' => $model->id], [
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
