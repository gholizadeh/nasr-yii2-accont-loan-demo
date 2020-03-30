<?php

use yii\helpers\Html;
use app\models\Segment;

if(\Yii::$app->user->hasAccess('segment', 'change'))
    echo Html::beginForm(['segment/change'], 'post', ['class' => 'seg-change']).
        '<div class="form-group">
            <p>'.\Yii::$app->user->full_name.' خوش آمدید!</p>'.
            '<div class="input-group">'.
                Html::dropDownList('segment', \Yii::$app->user->getSegment(), Segment::getSegments(), ['class' => 'form-control']).
                '<div class="input-group-append">'.
                    Html::submitButton('به روزرسانی', ['class' => 'btn btn-success']).
                '</div>
            </div> 
        </div>'.
    Html::endForm();