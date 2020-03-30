<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-form"> 

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-4">
    		<?= $form->field($model, 'role')->textInput(['maxlength' => true]) ?>
    	</div>
    	<div class="col-lg-4">
    		<?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
    	</div>
    	<div class="col-lg-4">
    		<?= $form->field($model, 'status')->dropDownList($statuses) ?>
    	</div>
    </div>
    <h3>مجوزها:</h3>
	<div class="row row-eq-height">
		<?php foreach ( $all_modules as $controller => $module ) { ?>
	
	 	<div class="col-sm-6 col-md-4 col-lg-3 mt-3">
	 		<div class="card">
	 			<div class="card-header"><?= $controller ?></div>
	 			<div class="card-body">
					<?= Html::checkboxList ( $controller, $act_modules, $module, [
						'tag' => 'table',
						'class' => 'table table-striped table-sm table-condensed',
						'item' => function($index, $label, $name, $checked, $value) {
			                return "<tr>
			                			<td><label>{$label}</label></td>
			                			<td>".Html::checkbox($name,$checked,['value'=>$value])."</td>
			                		</tr>";
			            }]
			        ) ?>
				</div>
			</div>
		</div>
		<?php }?>
	</div>

    <div class="form-group mt-3 text-left">
        <?= Html::submitButton('ذخیره', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>