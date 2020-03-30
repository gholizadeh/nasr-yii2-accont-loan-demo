<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\AccountDetailEntity;
use yii\data\ActiveDataProvider;
use app\components\ChangeLogBehavior;
use app\components\TimeLogBehavior;
use yii\db\Expression;

class AccountDetail extends AccountDetailEntity
{
    const TYPE_WITHDRAW = 1;
    const TYPE_DEPOSIT = 2;

    public $tr_user;

    public function behaviors(){
        return [
            TimeLogBehavior::className(),
            [
                'class' => ChangeLogBehavior::className(),
                'excludedAttributes' => ['modified_date','last_modified_by'],
            ]
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['search'] = ['id','amount','type','description','date','tr_user','reference_id'];
        $scenarios['insert'] = ['amount','description','reference_id'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'شناسه',
            'tr_user' => 'کاربر',
            'amount' => 'مبلغ',
            'type' => 'عملیات',
            'description' => 'شرح',
            'date' => 'تاریخ',
            'reference_id' => 'شماره پیگیری',
            'returned' => 'برگشتی', 
        ];
    }

    public static function getTypes(){
        return [
            self::TYPE_WITHDRAW => 'برداشت',
            self::TYPE_DEPOSIT => 'واریز',
        ];
    }

    public function beforeValidate()
    {
        if($this->isNewRecord) {
            $this->user_id = Yii::$app->user->id;
            $this->date = new Expression('UTC_TIMESTAMP()');
        }
        $this->modified_date = new Expression('UTC_TIMESTAMP()');
        $this->last_modified_by = Yii::$app->user->id;
        return parent::beforeValidate();
    }

    public function search($account_id, $params)
    {
        $query = AccountDetail::find()->joinWith(['user']);

        $query->where(['account_id' => $account_id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => [
                    'id',
                    'amount',
                    'type',
                    'description',
                    'date',
                    'tr_user' => [
                        'asc' => ['user.full_name' => SORT_ASC],
                        'desc' => ['user.full_name' => SORT_DESC],
                    ],
                    'reference_id'
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'description', $this->description])
              ->andFilterWhere(['like', 'reference_id', $this->reference_id])
              ->andFilterWhere(['like', 'user.full_name', $this->tr_user]);

        return $dataProvider;
    }
}