<?php

use app\models\Segment;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Client */
$this->title = 'افزودن مشتری'. $this->context->getSegmentLable();
$this->params['breadcrumbs'][] = ['label' => 'مشتریان', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-create">

	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'statuses' => $statuses
    ]) ?>

</div>
