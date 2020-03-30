<?php 

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\widgets\MonthPicker;
use yii\web\JsExpression;

$asset_limit = [
	'limited' => 'Choosen Assets',
	'filtered' => 'Filtered Assets',
	'all' => 'All Assets'
]
?>

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'mntboard-form']) ?>
	<div class="row">
		<div class="col-md-6 col-lg-3">
			<?= Html::label('Date(from)', '', ['class' => 'mt-2']) ?>
			<?= MonthPicker::widget([
				'name'  => 'start_date',
				'options' => ['class' => 'form-control'],
				'clientOptions' => [
					'pattern'=> 'yyyy-mm',
				    'startYear'=> date("Y") - 1,
				    'finalYear'=> date("Y"),
				],
				'bind' => [
					'monthpicker-change-year'=>new JsExpression("function(e, year){
						var monthes = function(year){
							var dis_month = [];
							if (year === '".(date("Y")-1)."') {
								 for(var m=1; m < ".date("m")."; ++m)
									 dis_month.push(m);
							 }else if (year === '".date("Y")."'){
								 for(var m=".date("m")." + 1; m <= 12; ++m)
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
			<?= Html::label('Date(to)', '', ['class' => 'mt-2']) ?>
			<?= MonthPicker::widget([
				'name'  => 'end_date',
				'options' => ['class' => 'form-control'],
				'clientOptions' => [
					'pattern'=> 'yyyy-mm',
				    'startYear'=> date("m") < 12 ? date("Y") : date("Y")+1,
				    'finalYear'=> date("m") < 6 ? date("Y") : date("Y")+1,
				],
				'bind' => [
					'monthpicker-change-year'=>new JsExpression("function(e, year){
						var monthes = function(year){
							var dis_month = [];
							if (year === '".(date("Y")+1)."') {
								for(var m=12; m > ".(date("m")-6)."; --m)
									dis_month.push(m);
							}else if (year === '".date("Y")."'){
								for(var m=".date("m")."; m > 0; --m)
									dis_month.push(m);
								for(var m=12; m > ".(date("m")+6)."; --m)
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
			<?= Html::label('Assets', '', ['class' => 'mt-2']) ?>
		    <?= Html::dropDownList('limited', '', $asset_limit, ['class' => 'form-control']) ?>
		</div>
	</div>

	<?= Html::hiddenInput('_csrf', Yii::$app->request->getCsrfToken()) ?>

	<?= Html::a(
	    Html::tag('i','',['class' => "fa fa-file-excel-o"]).
	    " Get MaintenanceBoard as excel", ['maintenance-board', 'Asset' => Yii::$app->getRequest()->getQueryParam('Asset')], 
	    [
	        'onClick'=>'makePostRequest(event,"mntboard-form")',
	        'class'=>'btn btn-md btn-success mt-3'
	    ]
	); ?>
<?= Html::endForm() ?>