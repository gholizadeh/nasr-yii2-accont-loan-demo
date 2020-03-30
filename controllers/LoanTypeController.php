<?php

namespace app\controllers;

use Yii;
use app\models\LoanType;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
/**
 * LoanTypeController implements the CRUD actions for LoanType model.
 */
class LoanTypeController extends BasicController
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
     * Lists all LoanType models.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new LoanType();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider
       ]);
    }

    /**
     * Displays a single LoanType model.
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
     * Creates a new LoanType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LoanType();

        if ($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LoanType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LoanType model.
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
            throw new NotAcceptableHttpException('به دلیل ارتباط این نوع تسهیلات با دیگر بخش ها امکان حذف آن وجود ندارد');
        }
    
        return $this->redirect(['index']);
    }

    /**
     * Finds the LoanType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LoanType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LoanType::find()->where(['id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('نوع تسهیلات وجود ندارد');
    }
}
