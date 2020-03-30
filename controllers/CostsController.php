<?php

namespace app\controllers;

use Yii;
use app\models\Costs;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
/**
 * CostsController implements the CRUD actions for Costs model.
 */
class CostsController extends BasicController
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
     * Lists all Costs models.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new Costs();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
           'statuses' => Costs::getStatuses()
       ]);
    }

    /**
     * Displays a single Costs model.
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
     * Creates a new Costs model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Costs();

        if ($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'statuses' => $model->getStatuses(),
        ]);
    }

    /**
     * Updates an existing Costs model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'statuses' => $model->getStatuses(),
        ]);
    }

    /**
     * Deletes an existing Costs model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $type = $this->findModel($id);
        try {
            $type->delete();
        } catch (\yii\db\Exception $e) {
            throw new NotAcceptableHttpException('به دلیل ارتباط این هزینه با دیگر بخش ها امکان حذف آن وجود ندارد');
        }
    
        return $this->redirect(['index']);
    }

    /**
     * Finds the Costs model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Costs the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Costs::find()->where(['id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('هزینه وجود ندارد');
    }
}
