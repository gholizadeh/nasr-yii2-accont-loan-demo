<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AccountType */ 
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'انواع حساب';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-type-index">
    <div class="row headline4">
        <div class="col-8 mt-1">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div class="col-4 text-left pr-0 mt-1">
            <?= Html::a(Html::tag('i', '', ['class' => "fa fa-plus"]).' افزودن نوع حساب', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <div class="table-wrapper table-responsive">
        <?php Pjax::begin(); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel, 
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                'description',
                [
                    'attribute' =>'charge_rate',
                    'filter' => false,
                    'value' => function($data, $row){
                        return $data->charge_rate." تومان";
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
                            return Html::a(Html::tag('i', '', ['class' => "fa fa-eye"]), $url);
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
