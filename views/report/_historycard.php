<?php 

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\MonthPicker;
use yii\web\JsExpression;
?>

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'historycard-form']) ?>
	<div class="row">
		<div class="col-md-3">
			<?= Html::label('From(month ago | leave empty for all)', '', ['class' => 'mt-2']) ?>
			<?= Html::input('text', 'month', '', ['class' => 'form-control', 'type' => 'number']) ?>
		</div>
		<div class="col-md-9">
			<?= Html::label('Include', '', ['class' => 'mt-2']) ?>
			<?= Html::checkboxList('items', '', [
				'work-order' => '<span>WorkOrder</span>',
				'job' => '<span>Job</span>',
				'failure' => '<span>Failure</span>',
				'movement' => '<span>Movement</span>'
			], ['class' => 'history-checkbox', 'encode' => false]) ?>
		</div>
	</div>

	<?= Html::hiddenInput('_csrf', Yii::$app->request->getCsrfToken()) ?>

	<?= Html::a(
	    Html::tag('i','',['class' => "fa fa-file-excel-o"]).
	    " Get Choosen Asset HistoryCard", ['history-card'], 
	    [
	        'onClick'=>'makePostRequest(event,"historycard-form")',
	        'class'=>'btn btn-md btn-success mt-3'
	    ]
	); ?>
<?= Html::endForm() ?>