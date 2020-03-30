<?php

namespace app\controllers;

use app\models\Account;
use Yii;
use app\models\Client;
use app\models\Loan;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
/**
 * ClientController implements the CRUD actions for Client model.
 */
class ClientController extends BasicController
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
     * Lists all Client models.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new Client();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
           'statuses' => $searchModel->getStatuses()
       ]);
    }

    /**
     * Displays a single Client model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $accountModel = new Account(['scenario' => 'search']);
        $loanModel = new Loan(['scenario' => 'search']);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'loanModel' => $loanModel, 
            'accountModel' => $accountModel, 
            'loanProvider' => $loanModel->search(Yii::$app->request->queryParams, ['account.client.client_id' => $id]),
            'accountProvider' => $accountModel->search(Yii::$app->request->queryParams, $id),
        ]);
    }

    /**
     * Creates a new Client model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Client();
        $model->scenario = 'insert';

        if ($model->load(Yii::$app->request->post())){
            $model = $this->findIfDuplicate($model);
            if ($model->save())
                return $this->redirect(['view', 'id' => $model->client_id]);
        }

        return $this->render('create', [
            'model' => $model,
            'statuses' => $model->getStatuses()
        ]);
    }

    /**
     * Updates an existing Client model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'update';

        if ($model->load(Yii::$app->request->post())){
            $model = $this->findIfDuplicate($model);
            if ($model->save())
                return $this->redirect(['view', 'id' => $model->client_id]);
        }

        return $this->render('update', [
            'model' => $model,
            'statuses' => $model->getStatuses()
        ]);
    }

    /**
     * Deletes an existing Client model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $client = $this->findModel($id);
        $transaction = Client::getDb()->beginTransaction();
        try {
            $client->unlinkAll('clientSegments', true);
            $client->delete();
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            throw new NotAcceptableHttpException('به دلیل ارتباط این مشتری با دیگر بخش ها امکان حذف آن وجود ندارد');
        }
    
        return $this->redirect(['index']);
    }

    /**
     * Finds the Client model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Client the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Client::find()->joinWith(['clientSegments'])->where(['client.client_id'=>$id, 'client_segment.segment_id'=>Yii::$app->user->getSegment()])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('مشتری مورد نظر در این صندوق دارای حساب نیست');
    }

    /**
     * Finds the Client model based on its phone number.
     */
    protected function findIfDuplicate($client)
    {
        if (($model = Client::find()->where(['cellphone'=>$client->cellphone])->one()) !== null) {
            $model->name = $client->name;
            $model->remarks = $client->remarks;
            return $model;
        }

        return $client;
    }
}
