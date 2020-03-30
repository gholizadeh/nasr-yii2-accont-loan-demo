<?php

namespace app\controllers;

use app\models\Segment;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;

class BasicController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $array = [
            'access' => Yii::$app->user->getAccess ( strtolower ( Yii::$app->controller->id ) ),
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => $this->Verbs()
            ],
        ];

        return $array;
    }

    public function Verbs(){
        return [];
    }

    public function getSegmentLable(){
        return (Yii::$app->user->segment_master) ? " (صندوق: ".Segment::findOne(Yii::$app->user->getSegment())->name.")" : "";
    }
}
