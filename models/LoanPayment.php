<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\LoanPaymentEntity;
use yii\data\ActiveDataProvider;
use app\components\ChangeLogBehavior;
use app\components\TimeLogBehavior;
use yii\db\Expression;

class LoanPayment extends LoanPaymentEntity
{
    const STAT_ACCEPT = 1;
    const STAT_DENIED = 2;

    public $user;

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
        $scenarios['search'] = ['id','loan','amount','date','user','status'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'شناسه',
            'loan' => 'وام',
            'amount' => 'مقدار',
            'date' => 'تاریخ',
            'modified_date' => 'آخرین ویرایش',
            'user' => 'کاربر',
            'status' => 'وضعیت'
        ];
    }

    public static function getStatuses(){
        return [
            self::STAT_ACCEPT => 'قبول',
            self::STAT_DENIED => 'رد',
        ];
    }

    public function beforeValidate()
    {
        if($this->isNewRecord) {
            $this->date = new Expression('UTC_TIMESTAMP()');
            $this->status = self::STAT_ACCEPT;
        }
        $this->modified_date = new Expression('UTC_TIMESTAMP()');
        $this->last_modified_by = Yii::$app->user->id;
        return parent::beforeValidate();
    }

    public function search($loan_id, $params)
    {
        $query = LoanPayment::find()->joinWith(['lastModifiedBy']);

        $query->where(['loan_id' => $loan_id]);

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
                    'loan' => [
                        'asc' => ['loan.name' => SORT_ASC],
                        'desc' => ['loan.name' => SORT_DESC],
                    ],
                    'amount',
                    'date',
                    'modified_date',
                    'user' => [
                        'asc' => ['user.full_name' => SORT_ASC],
                        'desc' => ['user.full_name' => SORT_DESC],
                    ],
                    'status'
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'date' => $this->date,
            'loan_payment.status' => $this->status
        ]);

        $query->andFilterWhere(['like', 'loan.name', $this->loan])
              ->andFilterWhere(['like', 'user.full_name', $this->user]);

        return $dataProvider;
    }
}