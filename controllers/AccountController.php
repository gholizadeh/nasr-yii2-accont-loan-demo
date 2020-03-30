<?php

namespace app\controllers;

use Yii;
use app\models\Account;
use app\models\AccountDetail;
use app\models\AccountType;
use app\models\Loan;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
/**
 * AccountController implements the CRUD actions for Account model.
 */
class AccountController extends BasicController
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
     * Lists all Account models.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new Account();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'accountTypes' => AccountType::getAccountTypes(),
           'statuses' => Account::getStatuses(),
           'dataProvider' => $dataProvider
       ]);
    }

    /**
     * Displays a single Account model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $loanModel = new Loan(['scenario' => 'search']);
        $trModel = new AccountDetail(['scenario' => 'search']);
        return $this->render('view', [
            'trModel' => $trModel,
            'trProvider' => $trModel->search($id, Yii::$app->request->queryParams),
            'loanModel' => $loanModel, 
            'loanProvider' => $loanModel->search(Yii::$app->request->queryParams, ['account.id' => $id]),
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Account model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Account();
        $model->scenario = 'insert';

        if ($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'statuses' => Account::getStatuses(),
            'accountTypes' => AccountType::getAccountTypes(),
        ]);
    }

    /**
     * Updates an existing Account model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'insert';
        $model->account_client = $model->client ? $model->client->name : '';
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'statuses' => Account::getStatuses(),
            'accountTypes' => AccountType::getAccountTypes(),
        ]);
    }

    public function actionDeposit($id){
        $model = $this->findModel($id);
        $model->scenario = 'settlement';
        $detailModel = new AccountDetail();
        $detailModel->scenario = 'insert';
        
        if ($detailModel->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())){
            $detailModel->account_id = $id;
            $detailModel->type = AccountDetail::TYPE_DEPOSIT;

            $model->remain_debit += $detailModel->amount;

            if($detailModel->save() && $model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('deposit_withdraw', [
            'model' => $model,
            'type' => AccountDetail::TYPE_DEPOSIT,
            'detailModel' => $detailModel
        ]);
    }

    public function actionReturn($id){
        if (($model = AccountDetail::find()->where(['id'=>$id])->one()) !== null && $model->returned !== 1) {
            $accountModel = $this->findModel($model->account_id);
            $detailModel = new AccountDetail(['scenario' => 'insert']);

            $detailModel->account_id = $accountModel->id;
            $detailModel->amount = $model->amount;
            $detailModel->description = '(تراکنش برگشتی شناسه "'.$model->id.'") '.$model->description;
            $detailModel->reference_id = $model->reference_id;
            $detailModel->returned = 1;

            if($model->type == AccountDetail::TYPE_DEPOSIT){
                $detailModel->type = AccountDetail::TYPE_WITHDRAW;
                $accountModel->remain_debit -= $detailModel->amount;
            }else{
                $detailModel->type = AccountDetail::TYPE_DEPOSIT;
                $accountModel->remain_debit += $detailModel->amount;
            }

            $model->returned = 1;
            
            if($detailModel->save() && $model->save() && $accountModel->save())
                return $this->redirect(['view', 'id' => $accountModel->id]);
        }
    }

    public function actionWithdraw($id){
        $model = $this->findModel($id);
        $model->scenario = 'settlement';
        $detailModel = new AccountDetail();
        $detailModel->scenario = 'insert';
        
        if ($detailModel->load(Yii::$app->request->post()) && $model->load(Yii::$app->request->post())){
            //check amount 
            if($detailModel->amount > $model->remain_debit){
                $detailModel->addError('amount', 'مبلغ برداشتی نباید از مانده حساب بیشتر باشد');
            }else{
                $detailModel->account_id = $id;
                $detailModel->type = AccountDetail::TYPE_WITHDRAW;

                $model->remain_debit -= $detailModel->amount;

                if($detailModel->save() && $model->save())
                    return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('deposit_withdraw', [
            'model' => $model,
            'type' => AccountDetail::TYPE_WITHDRAW,
            'detailModel' => $detailModel
        ]);
    }

    /**
     * Deletes an existing Account model.
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
            throw new NotAcceptableHttpException('به دلیل ارتباط این حساب با دیگر بخش ها امکان حذف آن وجود ندارد');
        }
    
        return $this->redirect(['index']);
    }

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Account::find()->where(['id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('حساب وجود ندارد');
    }
}
