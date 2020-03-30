<?php

use app\models\Defaults;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\tree\TreeViewInput;
use app\models\OrganizationChart;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<?php if(!empty($roles)){ ?>
    <div class="user-form">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <?= $form->field($model, 'user_group_id')->dropDownList($roles) ?>
                <?= $form->field($model, 'seg_id')->dropDownList($segments) ?>
            </div>
            <div class="col-md-6 col-lg-3">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'class' => 'form-control ltr']) ?>
                <?= $form->field($model, 'full_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6 col-lg-3">
                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true, 'class' => 'form-control ltr']) ?>
                <?= $form->field($model, 'passwordConfirm')->passwordInput(['maxlength' => true, 'class' => 'form-control ltr'])->label('تکرار کلمه عبور'); ?>
            </div>
            <div class="col-md-6 col-lg-3">
                <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>
                <div class="row">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status')->dropDownList($statuses) ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'segment_master')->dropDownList(Defaults::getYesNo()) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <?php foreach ($socialNames as $key => $value) {
                $soc_val = $model->socials[$key] ?? ''; ?>
                <div class="col-md-6 col-lg-3">
                    <?= Html::label($value) ?>
                    <?= Html::input('text', 'socials['.$key.']', $soc_val, ['class' => 'form-control ltr']) ?>
                </div>
            <?php } ?>
        </div>
        <div class="form-group text-left">
            <?= Html::submitButton('ذخیره', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php }else{ ?>
    <div class="alert alert-danger">قبل از افزودن کاربر حتما باید حداقل یک گروه کاربری تعریف کنید.</div>
<?php } ?>