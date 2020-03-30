<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Segment */

$this->title = 'افزودن صندوق';
$this->params['breadcrumbs'][] = ['label' => 'صندوق ها', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="segment-create">

	<div class="row headline4">
    	<h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'types' => $types
    ]) ?>

</div>
