<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
use app\models\Segment;
use app\models\Usergroup;
use app\components\notification\WorkOrderAssign;
/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BasicController
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new User();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
           'segments' => Segment::getSegments(),
           'statuses' => $searchModel->getStatuses(),
           'roles' => Usergroup::getRoles()
       ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = 'insert';

        if ($model->load(Yii::$app->request->post()) && $model->saveModel()) {
            return $this->redirect(['view', 'id' => $model->user_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'segments' => Segment::getSegments(),
            'roles' => Usergroup::getRoles(),
            'statuses' => $model->getStatuses(),
            'socialNames' => $model->getSocialNames()
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->keepPass();
        $model->scenario='update';

        if ($model->load(Yii::$app->request->post()) && $model->saveModel()) {
            return $this->redirect(['view', 'id' => $model->user_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'segments' => Segment::getSegments(),
            'roles' => Usergroup::getRoles(),
            'statuses' => $model->getStatuses(),
            'socialNames' => $model->getSocialNames()
        ]);
    }

    public function actionTestNotif(){
        $recipient = $this->findModel(2);
        $notification = new WorkOrderAssign(['params' => ['wo_id' => 76]]);

        Yii::$app->notifications->send($recipient, $notification);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
        } catch (\Exception $e) {
            throw new NotAcceptableHttpException('اطلاعات این کاربر به مواردی متصل است. به منظور مسدودسازی دسترسی می توانید کاربر را غیرفعال کنید.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::find()->with(['userGroup'])->where(['user_id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('کاربر مورد نظر یافت نشد.');
    }
}
