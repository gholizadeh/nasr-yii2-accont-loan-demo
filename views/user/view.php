<?php

use app\models\Defaults;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'کاربران', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <div class="headline4 text-left mb-2">
        <?= Html::a('ویرایش', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('حذف', ['delete', 'id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'آیا مطمئن هستید?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="table-wrapper pt-3">
        <div class="row">
            <div class="col-12">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'user_id',
                        'segment' => [
                            'label' => 'صندوق',
                            'attribute' => 'seg.name'
                        ],
                        [
                            'label' => 'کاربر سیستمی',
                            'value' => Defaults::getYesNo()[$model->segment_master]
                        ],
                        'userGroup.role',
                        'email:email',
                        'full_name',
                        'remarks',
                        [
                            'label' => 'وضعیت',
                            'value' => $model->getStatuses()[$model->status],
                        ],
                        [
                            'label' => 'کانال های ارتباطی',
                            'format'=>'raw',
                            'value' => function($data){
                                $result = '';
                                //var_dump($data->getSocialNames())
                                foreach ($data->getSocialNames() as $key => $value) {
                                    $result .= isset($data->socials[$key]) ? $value.": <span class='ltr d-inline-block'>".$data->socials[$key]."</span><br>" : '';
                                }
                                return $result;
                            }
                        ]
                    ],
                    'options' =>['class' => 'table table-striped table-bordered table-sm table-hover detailView']
                ]) ?>
            </div>
        </div>
    </div>
</div>
