<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AccountType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'انواع حساب', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-type-view">

    <div class="headline4 text-left mb-2">
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
                        'description'
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        [
                            'label' => 'شارژ ماهانه',
                            'value' => $model->charge_rate." تومان",
                        ]
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
        </div>
    </div>
</div>
