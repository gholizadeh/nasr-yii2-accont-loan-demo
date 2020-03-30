<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Cost */

$this->title = 'افزودن هزینه';
$this->params['breadcrumbs'][] = ['label' => 'انواع هزینه', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cost-create">

	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'statuses' => $statuses
    ]) ?>

</div>
