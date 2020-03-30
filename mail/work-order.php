<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<h2>New WrokOrder</h2>
<p>You have been assigned to <?= Html::a('WorkOrder #'.$model->wo_code, ['/work-order/update', 'id' => $model->work_order_id]) ?> click on the link to update the WorkOrder</p>