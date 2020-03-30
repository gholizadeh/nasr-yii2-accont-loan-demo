<?php

namespace app\controllers;

use Yii;
use app\models\Usergroup;
use app\models\Access;
use app\models\Module;
use yii\helpers\ArrayHelper;

/**
 * UserController implements the CRUD actions for Usergroup model.
 */
class UserGroupController extends BasicController
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
     * Lists all Usergroup.
     * @return mixed
     */
    public function actionIndex()
    {
       $searchModel = new Usergroup();
       $searchModel->scenario = 'search';
       $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
       return $this->render('index', [
           'searchModel' => $searchModel, 
           'dataProvider' => $dataProvider,
           'statuses' => $searchModel->getStatuses()
       ]);
    }

    /**
     * Creates a new Usergroup.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Usergroup();
        $modules = Module::find()->all();

        $all_modules = ArrayHelper::map($modules, 'module_id', 'module_desc', 'controller');
        $act_modules = [];

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($this->saveAccess($model->user_group_id, $all_modules))
                return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'act_modules' => $act_modules,
            'all_modules' => $all_modules,
            'statuses' => $model->getStatuses()
        ]);
    }

    /**
     * Updates an existing Usergroup.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model_c = new Usergroup();
        $model = $model_c->getUserGroup($id);
        $modules = Module::find()->all();

        $all_modules = ArrayHelper::map($modules, 'module_id', 'module_desc', 'controller');
        $act_modules = ArrayHelper::getColumn($model->modules, 'module_id');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($this->saveAccess($model->user_group_id, $all_modules))
                return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
            'act_modules' => $act_modules,
            'all_modules' => $all_modules,
            'statuses' => $model->getStatuses()
        ]);
    }

    private function saveAccess($user_group_id, $all_modules){
        $user_access = [];
        $post = Yii::$app->request->post();

        foreach (array_keys($all_modules) as $controller) {
            if (isset($post[$controller])) {
                foreach ( $post[$controller] as $checked ) {
                    array_push ( $user_access, [
                        'module_id' => $checked,
                        'user_group_id' => $user_group_id 
                    ]);
                }
            }
        }

        Access::deleteAll('user_group_id = :ugid',[':ugid'=>$user_group_id]);
        return Access::insertAccess ( $user_access );
    }
    /**
     * Deletes an existing Usergroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Usergroup::find()->where(['user_group_id'=>$id])->one()->delete();
        return $this->redirect(['index']);
    }
}
