<?php 

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Movement;
use yii\jui\DatePicker;

$op_start = [
	'=' => 'equal',
	'>' => 'after',
];
$op_end = [
	'<' => 'before',
];
$asset_limit = [
	'limited' => 'Choosen Assets',
	'filtered' => 'Filtered Assets',
	'all' => 'All Assets'
]
?>

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'movement-form']) ?>
	<div class="row">
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Type', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('Movement[type]', '', $mvTypes, ['class' => 'form-control', 'prompt'=>'']) ?>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Origin', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('Movement[origin]', '', $locations, ['class' => 'form-control', 'prompt'=>'']) ?>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Destination', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('Movement[destination]', '', $locations, ['class' => 'form-control', 'prompt'=>'']) ?>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Status', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('Movement[status]', '', $mvStats, ['class' => 'form-control', 'prompt'=>'']) ?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Date (from)', '', ['class' => 'mt-2']) ?>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<?= Html::dropDownList('Movement[date_start_op]', '', $op_start,['class' => 'form-control datepicker-op']) ?>
				</div>
				<?= DatePicker::widget([
					'name'  => 'Movement[date_start]',
					'dateFormat' => 'yyyy-MM-dd',
					'options' => [
						'class' => 'form-control'
					]
				]); ?>
			</div>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Date (to)', '', ['class' => 'mt-2']) ?>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<?= Html::dropDownList('Movement[date_end_op]', '', $op_end,['class' => 'form-control datepicker-op']) ?>
				</div>
				<?= DatePicker::widget([
					'name'  => 'Movement[date_end]',
					'dateFormat' => 'yyyy-MM-dd',
					'options' => [
						'class' => 'form-control'
					]
				]); ?>
			</div>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Assets', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('limited', '', $asset_limit, ['class' => 'form-control']) ?>
		</div>
	</div>

	<?= Html::hiddenInput('_csrf', Yii::$app->request->getCsrfToken()) ?>

	<?= Html::a(
	    Html::tag('i','',['class' => "fa fa-file-excel-o"]).
	    " Get movements as excel", ['movement', 'Asset' => Yii::$app->getRequest()->getQueryParam('Asset')], 
	    [
	        'onClick'=>'makePostRequest(event,"movement-form")',
	        'class'=>'btn btn-md btn-success mt-3'
	    ]
	); ?>
<?= Html::endForm() ?>