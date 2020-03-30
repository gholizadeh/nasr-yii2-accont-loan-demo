<?php

namespace app\controllers;

use app\models\Account;
use app\models\Costs;
use app\models\entities\LoanCostsEntity;
use Yii;
use app\models\Loan;
use app\models\LoanPayment;
use app\models\LoanType;
use yii\web\NotFoundHttpException;
use yii\web\NotAcceptableHttpException;
/**
 * LoanController implements the CRUD actions for Loan model.
 */
class LoanController extends BasicController
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
     * Lists all Loan models.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new Loan();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'loanTypes' => LoanType::getLoanTypes(),
           'statuses' => Loan::getStatuses(),
           'dataProvider' => $dataProvider
       ]);
    }

    /**
     * Displays a single Loan model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $trModel = new LoanPayment(['scenario' => 'search']);
        return $this->render('view', [
            'trModel' => $trModel,
            'trProvider' => $trModel->search($id, Yii::$app->request->queryParams),
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Loan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new Loan();
        $model->first_installment = date('Y-m-d');

        $post = Yii::$app->request->post();

        if ($model->load($post)) {
            if($model->save()){

                if(isset($post['costs'])){
                    $this->saveCosts($model,$post['costs']);
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                var_dump($model->errors);exit;
            }
        }

        return $this->render('create', [
            'model' => $model,
            'statuses' => Loan::getStatuses(),
            'loanTypes' => LoanType::getLoanTypes(),
            'accounts' => Account::getClientAccounts($id),
            'costs' => Costs::getCosts('name'),
            'cost_amounts' => Costs::getCosts('amount'),
        ]);
    }

    /**
     * Updates an existing Loan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->remain != $model->Installment_count)
            return $this->redirect(['view', 'id' => $model->id]);

        $selected_costs = Loan::getLoanSelectedCosts($id);
        
        $post = Yii::$app->request->post();
        if ($model->load($post)) {
            if($model->save()){
                if(isset($post['costs'])){
                    $this->saveCosts($model,$post['costs']);
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'statuses' => Loan::getStatuses(),
            'loanTypes' => LoanType::getLoanTypes(),
            'accounts' => Account::getClientAccounts($model->account->client_id),
            'selected_costs' => $selected_costs,
            'costs' => Costs::getCosts('name'),
            'cost_amounts' => Costs::getCosts('amount'),
        ]);
    }

    private function saveCosts($loan, $post){
        //delete olds if exist
        $loan->unlinkAll('loanCosts', true);

        //insert new
        foreach ($post as $loanCosts) {
            $lc_model = new LoanCostsEntity();
            $lc_model->load(['LoanCostsEntity' => $loanCosts]);
            $loan->link('loanCosts', $lc_model);
        }
    }

    /**
     * Deletes an existing Loan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $loan = $this->findModel($id);
        $transaction = Loan::getDb()->beginTransaction();
        try {
            $loan->unlinkAll('loanCosts', true);
            $loan->delete();
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            throw new NotAcceptableHttpException('به دلیل ارتباط این تسهیلات با دیگر بخش ها امکان حذف آن وجود ندارد');
        }
    
        return $this->redirect(['index']);
    }

    public function actionUpdateStatus(){
        $allStartedLoans = Loan::find()->where(['status'=>Loan::STAT_START])->all();
        foreach($allStartedLoans as $loan){
            $loan->scenario = 'remain';
            $loan->updateStatus();
            $loan->save();
        }

        return $this->redirect(['index']);
    }

    public function actionDeposit($id){
        $model = $this->findModel($id);
        if($model->remain == 0)
            return $this->redirect(['view', 'id' => $model->id]);

        $model->scenario = 'remain';
        $install = (int)($model->amount / $model->Installment_count);

        $paymentModel = new LoanPayment();
        
        if ($paymentModel->load(Yii::$app->request->post())){
            $paymentModel->loan_id = $id;

            $model->remain -= (int)($paymentModel->amount / $install);
            $model->updateStatus();

            if($paymentModel->save() && $model->save())
                return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('deposit', [
            'model' => $model,
            'install' => $install,
            'paymentModel' => $paymentModel
        ]);
    }

    public function actionReturn($id){
        if (($model = LoanPayment::find()->where(['id'=>$id])->one()) !== null && $model->status !== LoanPayment::STAT_DENIED) {
            $loanModel = $this->findModel($model->loan_id);
            $loanModel->scenario = 'remain';

            $install = (int)($loanModel->amount / $loanModel->Installment_count);

            $model->status = LoanPayment::STAT_DENIED;
            $loanModel->remain += (int)($model->amount / $install);
            $loanModel->updateStatus();
                        
            if($model->save() && $loanModel->save())
                return $this->redirect(['view', 'id' => $loanModel->id]);
        }
    }

    /**
     * Finds the Loan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Loan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Loan::find()->where(['id'=>$id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('حساب وجود ندارد');
    }
}
