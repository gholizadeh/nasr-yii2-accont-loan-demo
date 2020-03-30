<?php
/**
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use Yii;
use app\models\entities\LoanEntity;
use yii\data\ActiveDataProvider;
use app\components\ChangeLogBehavior;
use app\components\TimeLogBehavior;
use app\models\entities\LoanCostsEntity;
use DateTime;
use yii\db\Expression;
use yii\db\Query;

class Loan extends LoanEntity
{
    const STAT_START = 1;
    const STAT_CLEARED = 2;
    const STAT_DUE = 3;
    const STAT_SOONER_SETTLE = 4;
    const STAT_DUE_SETTLE = 5;

    public $loan_user;
    public $loan_account;
    public $client;
    public $type;

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
        $scenarios['search'] = ['id', 'description', 'date', 'type', 'amount', 'loan_account', 'loan_user', 'client', 'remain', 'status'];
        $scenarios['remain'] = ['remain'];
        return $scenarios;
    }

    public function getLoanSelectedCosts($id){
        return LoanCostsEntity::find()->where(["loan_id" => $id])->asArray()->all();
    }

    public function attributeLabels()
    {
        return [
            'id' => 'شناسه',
            'date' => 'تاریخ اعطا',
            'type' => 'نوع',
            'loan_type' => 'نوع',
            'account_id' => 'حساب',
            'loan_account' => 'حساب',
            'loan_user' => 'کاربر',
            'client' => 'صاحب حساب',
            'amount' => 'مبلغ',
            'Installment_count' => 'تعداد اقساط',
            'first_installment' => 'شروع اقساط',
            'remain' => 'مانده',
            'description' => 'شرح',
            'status' => 'وضعیت',
            'modified_date' => 'تاریخ ویرایش', 
            'last_modified_by' => 'ویرایشگر', 
        ];
    }

    public static function getStatuses(){
        return [
            self::STAT_START => 'در جریان',
            self::STAT_CLEARED => 'تسویه',
            self::STAT_DUE => 'سررسید',
            self::STAT_SOONER_SETTLE => 'تسویه زودتر',
            self::STAT_DUE_SETTLE => 'تسویه پس از سررسید',
        ];
    }

    public function beforeValidate()
    {
        if($this->isNewRecord) {
            $this->user_id = Yii::$app->user->id;
            $this->date = new Expression('UTC_TIMESTAMP()');
        }
        
        if($this->scenario != 'remain'){
            $this->remain = $this->Installment_count;
            $this->modified_date = new Expression('UTC_TIMESTAMP()');
            $this->last_modified_by = Yii::$app->user->id;
        }

        return parent::beforeValidate();
    }

    public static function segmentLoanCount(){
        return self::find()->joinWith(['account'])
                    ->where(['account.seg_id'=>Yii::$app->user->getSegment()])
                    ->andWhere(['in', 'loan.status', [self::STAT_DUE,self::STAT_START]])
                    ->count();
    }
    
    public static function segmentLoanSum(){
        return (new Query)->from('loan')
                          ->join('RIGHT JOIN', 'account', 'loan.account_id=account.id')
                          ->where('account.seg_id='.Yii::$app->user->getSegment())
                          ->sum('loan.amount');
    }
    
    public static function segmentCurrentLoanSum(){
        return (new Query)->from('loan')
                          ->join('RIGHT JOIN', 'account', 'loan.account_id=account.id')
                          ->where('account.seg_id='.Yii::$app->user->getSegment())
                          ->andWhere(['in', 'loan.status', [self::STAT_DUE,self::STAT_START]])
                          ->sum('loan.amount');
	}

    public function updateStatus(){
        $settlementDate = strtotime("+".$this->Installment_count." months", strtotime($this->first_installment));
        $MonthAgo = strtotime("-1 month");
        $MonthLater = strtotime("+1 month");
        if($this->remain == 0){
            if ($MonthAgo <= $settlementDate && $settlementDate <= $MonthLater){ //SETTLE
                $this->status = self::STAT_CLEARED;
            }elseif($MonthAgo > $settlementDate){ //DUE SETTLE
                $this->status = self::STAT_DUE_SETTLE;
            }elseif($settlementDate > $MonthLater){//SOON_SETTLE
                $this->status = self::STAT_SOONER_SETTLE;
            }
        }else{
            $d1 = new DateTime($this->first_installment);
            $d2 = new DateTime();
            $diff = $d2->diff($d1);

            $months = (int)round($diff->y * 12 + $diff->m + $diff->d / 30);

            if(($months > $this->Installment_count && $this->remain > 0) || ($months - ($this->Installment_count - $this->remain)) > 0)
                $this->status = self::STAT_DUE;
            else
                $this->status = self::STAT_START;
        }
    }

    public function search($params, $by = [])
    {
        $query = Loan::find()->joinWith(['loanType', 'user', 'account', 'account.client']);

        $query->where(['account.seg_id'=>Yii::$app->user->getSegment()]);

        if(isset($by['field'])){
            $query->andWhere([$by['field'] => $by['value']]);
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
                    'amount',
                    'date',
                    'type' => [
                        'asc' => ['loan_type.name' => SORT_ASC],
                        'desc' => ['loan_type.name' => SORT_DESC],
                    ],
                    'loan_user' => [
                        'asc' => ['user.name' => SORT_ASC],
                        'desc' => ['user.name' => SORT_DESC],
                    ],
                    'client' => [
                        'asc' => ['client.name' => SORT_ASC],
                        'desc' => ['client.name' => SORT_DESC],
                    ],
                    'loan_account' => [
                        'asc' => ['account.name' => SORT_ASC],
                        'desc' => ['account.name' => SORT_DESC],
                    ],
                    'remain',
                    'description',
                    'status'
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'loan_type' => $this->type,
            'amount' => $this->amount,
            'loan.status' => $this->status,
            'date' => $this->date,
        ]);

        $query->andFilterWhere(['like', 'loan.description', $this->description])
              ->andFilterWhere(['like', 'user.name', $this->user])
              ->andFilterWhere(['like', 'client.name', $this->client])
              ->andFilterWhere(['like', 'account.name', $this->account]);

        return $dataProvider;
    }
}