<?php

namespace app\controllers;

use app\models\Account;
use Yii;
use app\models\LoginForm;
use app\models\Asset;
use app\models\Client;
use app\models\Loan;
use app\models\Location;
use app\models\MaintenanceType;

class SiteController extends BasicController
{
    public function Verbs(){
        return [
            'logout' => ['post']
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $loanModel = new Loan(['scenario' => 'search']);
        $accountModel = new Account(['scenario' => 'search']);
        $clientModel = new Client(['scenario' => 'search']);        

        return $this->render('index', [
           'loanModel' => $loanModel, 
           'accountModel' => $accountModel, 
           'clientModel' => $clientModel, 
           'loanProvider' => $loanModel->search(Yii::$app->request->queryParams),
           'accountProvider' => $accountModel->search(Yii::$app->request->queryParams),
           'clientProvider' => $clientModel->search(Yii::$app->request->queryParams)
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = 'login';
        
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
