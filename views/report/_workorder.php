<?php 

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\WorkOrder;
use yii\jui\DatePicker;
use app\widgets\Select2;

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

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'workorder-form']) ?>

	<div class="row">
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Status', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('WorkOrder[status]', '', $woStatuses, ['prompt'=>'Select one...', 'class' => 'form-control']) ?>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Type', '', ['class' => 'mt-2']) ?>
			<?= Html::dropDownList('WorkOrder[type]', '', $woTypes, ['prompt'=>'Select one...', 'class' => 'form-control']) ?>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Assigned to', '', ['class' => 'mt-2']) ?>
			<?= Html::dropDownList('WorkOrder[assigned_to]', '', $technicians, ['prompt'=>'Select one...', 'class' => 'form-control']) ?>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Mantenance', '', ['class' => 'mt-2']) ?>
		    <?= Select2::widget([
				    'name' => 'WorkOrder[mnt_id][]',
				    'items' => $maintenances,
				    'clientOptions' => [
				        'multiple' => true,
				        'class' => 'form-control'
				    ]
				]); 
			?>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Done date (from)', '', ['class' => 'mt-2']) ?>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<?= Html::dropDownList('WorkOrder[done_date_start_op]', '', $op_start,['class' => 'form-control datepicker-op']) ?>
				</div>
				<?= DatePicker::widget([
					'name'  => 'WorkOrder[done_date_start]',
					'dateFormat' => 'yyyy-MM-dd',
					'options' => [
						'class' => 'form-control'
					]
				]); ?>
			</div>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Done date (to)', '', ['class' => 'mt-2']) ?>
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<?= Html::dropDownList('WorkOrder[done_date_end_op]', '', $op_end,['class' => 'form-control datepicker-op']) ?>
				</div>
				<?= DatePicker::widget([
					'name'  => 'WorkOrder[done_date_end]',
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
	    " Get work orders as excel", ['work-order', 'Asset' => Yii::$app->getRequest()->getQueryParam('Asset')], 
	    [
	        'onClick'=>'makePostRequest(event,"workorder-form")',
	        'class'=>'btn btn-md btn-success mt-3'
	    ]
	); ?>
<?= Html::endForm() ?>