<?php 

use yii\helpers\Html;

$this->title = 'KPI Report'; 
?>

<div class="card light-shadow mb-2">
    <div class="card-header">
		<h3>KPI Report</h3>
    </div>
    <div class="card-body">
		<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'kpi-form']) ?>

			<div class="row">
				<div class="col-md-6 col-lg-3">
					<?= Html::label('Physical location', '', ['class' => 'mt-2']) ?>
				    <?= Html::dropDownList('location', '', $locations, ['class' => 'form-control']) ?>
				</div>
			</div>

			<?= Html::hiddenInput('_csrf', Yii::$app->request->getCsrfToken()) ?>

			<?= Html::a(
			    Html::tag('i','',['class' => "fa fa-file-excel-o"]).
			    " Get KPI as excel", ['kpi'], 
			    [
			        'onClick'=>'makePostRequest(event,"kpi-form")',
			        'class'=>'btn btn-md btn-success mt-3'
			    ]
			); ?>
		<?= Html::endForm() ?>
    </div>
</div>
<script type="text/javascript">
	function makePostRequest(e, from_id) {
	    e.preventDefault();
	    var jForm = from_id ? $('#'+from_id) : $('<form></form>');
	    jForm.attr('action', $(event.target).attr('href'));
	    jForm.attr('method', 'post');
	    jForm.submit();
	}
</script>