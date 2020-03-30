<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'انصارالحجه';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile("@web/css/login.css", ['media' => 'screen']);
?>
<div class="login-head">
    <h2 class="text-center mb-3"><?= Html::encode($this->title) ?></h2>
</div>
<div class="form">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => ['template' => "{input}<span class='input-focus'></span> {error}"],
    ]); ?>
        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Email', 'class'=>'form-control ltr']) ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password', 'class'=>'form-control ltr']) ?>
        <?= $form->field($model, 'rememberMe', ['labelOptions'=>['class'=>'']])->checkbox([
            'template' => "{label} {input}",
        ]) ?>
        <?= Html::submitButton('ورود', ['class' => 'btn', 'name' => 'login-button']) ?>
    <?php ActiveForm::end(); ?>

    <?php $form = ActiveForm::begin([
        'id' => 'forget-form',
        'layout' => 'horizontal',
        'fieldConfig' => ['template' => "{input}{error}"],
    ]); ?>
        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Email', 'id'=>'resetEmail']) ?>
        <?= Html::submitButton('Reset', ['class' => 'btn', 'name' => 'reset-button']) ?>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    /*
    $('.message a').click(function(){
       $('form').animate({height: "toggle", opacity: "toggle"}, "slow");
    });
    */
</script>