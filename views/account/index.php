<?php

use yii\bootstrap4\Breadcrumbs;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\Account */ 
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'حساب ها'. $this->context->getSegmentLable();
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-index">
    <div class="row headline4">
        <div class="col-8 mt-1">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-4 text-left pr-0 mt-1">
            <?= Html::a(Html::tag('i', '', ['class' => "fa fa-plus"]).' افزودن حساب', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="table-wrapper table-responsive">
        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel, 
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'id',
                [
                    'attribute' => 'account_client',
                    'value' => 'client.name'
                ],
                'name',
                [
                    'attribute' =>'create_date',
                    'value' =>  function($data, $row){
                        return isset($data->create_date) ? \yii::$app->pDate->JalaliDate($data->create_date) : '(خطا)';
                    }
                ],
                [
                    'attribute' => 'account_type',
                    'filter' => $accountTypes,
                    'value' => 'accountType.name'
                ],
                [
                    'attribute' =>'status',
                    'filter' => $statuses,
                    'value' => function($data, $row)  use ($statuses){
                        return $statuses[$data->status];
                    }
                ],
                [
                    'attribute' =>'remain_debit',
                    'filter' => false,
                    'value' => function($data, $row){
                        return $data->remain_debit." تومان";
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
                            return Html::a(Html::tag('i', '', ['class' => "fa fa-edit"]), $url);
                        },
                        'delete' => function ($url, $model, $key) {
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
