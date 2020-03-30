<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Segment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'صندوق ها', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="segment-view">

    <div class="headline4 text-left mb-2">
        <?= Html::a('ویرایش', ['update', 'id' => $model->seg_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('حذف', ['delete', 'id' => $model->seg_id], [
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
                        'seg_id',
                        'name'
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
                            'label' => 'نوع',
                            'value' => $model->getTypes()[$model->type],
                        ]
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
        </div>
    </div>
</div>
