<?php 

use yii\helpers\Html;
$asset_limit = [
    'limited' => 'Choosen Assets',
    'filtered' => 'Filtered Assets',
    'all' => 'All Assets'
]
?>

<?= Html::beginForm('', 'post', ['enctype' => 'multipart/form-data', 'id' => 'asset-form']) ?>

<div class="row">
    <div class="col-md-6 col-lg-3">
        <?= Html::label('Assets', '', ['class' => 'mt-2']) ?>
        <?= Html::dropDownList('limited', '', $asset_limit, ['class' => 'form-control']) ?>
    </div>
    <div class="col-md-6 col-lg-3 d-flex">
        <span class="d-flex align-items-end">
        <?= Html::a(
            Html::tag('i','',['class' => "fa fa-file-excel-o"]).
            " Get assets as excel", ['asset-report', 'Asset' => Yii::$app->getRequest()->getQueryParam('Asset')], 
            [
                'onClick'=>'makePostRequest(event,"asset-form")',
                'class'=>'btn btn-md btn-success'
            ]
        ); ?>
    </span>
    </div>
</div>

    <?= Html::hiddenInput('_csrf', Yii::$app->request->getCsrfToken()) ?>
<?= Html::endForm() ?>