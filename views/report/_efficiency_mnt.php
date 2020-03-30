<?php 

use yii\helpers\Html;
use app\widgets\MonthPicker;
use yii\web\JsExpression;
use app\widgets\Select2;
?>

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'efficiency-mnt-form']) ?>

	<div class="row">
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Mantenances', '', ['class' => 'mt-2']) ?>
			<?= Select2::widget([
				    'name' => 'mnt_id[]',
				    'items' => $maintenances,
				    'clientOptions' => [
				        'multiple' => true,
				        'class' => 'form-control',
				        'prompt'=> null
				    ]
				]); 
			?>
		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Date(to compare)', '', ['class' => 'mt-2']) ?>
			<?= MonthPicker::widget([
				'name'  => 'compare_date',
				'options' => ['class' => 'form-control'],
				'clientOptions' => [
					'pattern'=> 'yyyy-mm',
				    'startYear'=> '2018',
				    'finalYear'=> date("Y"),
				],
				'bind' => [
					'monthpicker-change-year'=>new JsExpression("function(e, year){
						var monthes = function(year){
							var dis_month = [];
							if (year === '".date("Y")."'){
								for(var m=12; m > ".(date("m")-1)."; --m)
									dis_month.push(m);
							}
							return dis_month;
						}
						$(this).monthpicker('disableMonths', []);
						$(this).monthpicker('disableMonths', monthes(year));
                    }"),
				]
			]); ?>

		</div>
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Physical location', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('location', '', $locations, ['prompt'=>'All', 'class' => 'form-control']) ?>
		</div>
	</div>

	<?= Html::hiddenInput('_csrf', Yii::$app->request->getCsrfToken()) ?>

	<?= Html::a(
	    Html::tag('i','',['class' => "fa fa-file-excel-o"]).
	    " Get Efficiency as excel", ['mnt-efficiency'], 
	    [
	        'onClick'=>'makePostRequest(event,"efficiency-mnt-form")',
	        'class'=>'btn btn-md btn-success mt-3'
	    ]
	); ?>
<?= Html::endForm() ?>