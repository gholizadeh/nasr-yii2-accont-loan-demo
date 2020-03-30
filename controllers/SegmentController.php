<?php

namespace app\controllers;

use Yii;
use app\models\Segment;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
/**
 * SegmentController implements the CRUD actions for Segment model.
 */
class SegmentController extends BasicController
{
    /**
     * {@inheritdoc}
     */
    public function Verbs()
    {
        return [
            'delete' => ['POST']
        ];
    }

    /**
     * Lists all Segment models.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new Segment();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
           'types' => $searchModel->getTypes(),
       ]);
    }

    /**
     * Displays a single Segment model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Segment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Segment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->seg_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'types' => $model->getTypes()
        ]);
    }

    /**
     * Updates an existing Segment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->seg_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'types' => $model->getTypes()
        ]);
    }

    /**
     * Deletes an existing Segment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @throws NotAcceptableHttpException if segment used
     */
    public function actionDelete($id)
    {
        $segment = $this->findModel($id);
        try {
            $segment->delete();
        } catch (\yii\db\Exception $e) {
            throw new NotAcceptableHttpException('به دلیل ارتباط این صندوق با دیگر بخش ها امکان حذف آن وجود ندارد');
        }
        return $this->redirect(['index']);
    }

    /**
     * Change user current Segment.
     * If user has master segment can change it's segment temp.
     * @param integer $id
     * @throws NotAcceptableHttpException if user segment is not master
     */
    public function actionChange()
    {
        $seg_id = Yii::$app->request->post()['segment'];
        $this->findModel($seg_id); //to make sure id is valid
        \Yii::$app->user->setSegment($seg_id);

        return $this->redirect(['site/index']);
    }

    /**
     * Finds the Segment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Segment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Segment::find()->where(['seg_id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('صندوق مورد نظر یافت نشد.');
    }
}
