<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use app\components\ChangeLogBehavior;
use app\components\TimeLogBehavior;
use Yii;
use app\models\entities\AccountEntity;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Account extends AccountEntity
{
    const STAT_ACTIVE = 1;
    const STAT_DEACTIVE = 2;
    const STAT_BLOCKED = 3;

    public $account_client;
    public $account_type;

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
        $scenarios['search'] = ['id','account_client','remain_debit','account_type','name','create_date','status','settlement_til'];
        $scenarios['insert'] = ['id','client_id','type','name','status','settlement_til'];
        $scenarios['settlement'] = ['settlement_til'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'شناسه',
            'account_client' => 'صاحب حساب',
            'name' => 'عنوان',
            'create_date' => 'تاریخ افتتاح', 
            'modified_date' => 'آخرین ویرایش', 
            'last_modified_by' => 'آخرین ویرایشگر', 
            'remain_debit' => 'موجودی',
            'account_type' => 'نوع حساب',
            'status' => 'وضعیت',
            'settlement_til' => 'تسویه تا تاریخ',
        ];
    }

    public function beforeValidate()
    {
        if($this->isNewRecord) {
            $this->seg_id = Yii::$app->user->getSegment();
            $this->create_date = new Expression('UTC_TIMESTAMP()');
        }
        return parent::beforeValidate();
    }

    public static function getStatuses(){
        return [
            self::STAT_ACTIVE => 'فعال',
            self::STAT_DEACTIVE => 'غیر فعال',
            self::STAT_BLOCKED => 'مسدود'
        ];
    }

    public static function getClientAccounts($id){
        return ArrayHelper::map(self::find()->where(['client_id' => $id])->all(), 'id', 'name');
    }

    public static function segmentAccountsCount(){
        return self::find()
                    ->where(['seg_id'=>Yii::$app->user->getSegment()])
                    ->count();
    }
    
    public static function segmentAccountsSum(){
        return (new Query)->from('account')->where('seg_id='.Yii::$app->user->getSegment())->sum('remain_debit');
	}

    public function search($params, $client_id = null)
    {
        $query = Account::find()->joinWith(['client','accountType']);

        //always use a segment at a time
        $query->where(['account.seg_id'=>Yii::$app->user->getSegment()]);

        if((int)$client_id > 0){
            $query->andWhere(['account.client_id' => $client_id]);
        }

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
                    'account_client' => [
                        'asc' => ['client.name' => SORT_ASC],
                        'desc' => ['client.name' => SORT_DESC],
                    ],
                    'name',
                    'account_type' => [
                        'asc' => ['accountType.name' => SORT_ASC],
                        'desc' => ['accountType.name' => SORT_DESC],
                    ],
                    'remain_debit',
                    'status',
                    'create_date',
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'account.id' => $this->id,
            'account.status' => $this->status,
            'account.type' => $this->account_type
        ]);

        $query->andFilterWhere(['like', 'account.name', $this->name])
              ->andFilterWhere(['like', 'client.name', $this->account_client]);

        return $dataProvider;
    }
}