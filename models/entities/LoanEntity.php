<?php

namespace app\models\entities;

use Yii;
use app\models\Account;
use app\models\LoanPayment;
use app\models\User;
use app\models\LoanType;

/**
 * This is the model class for table "loan".
 *
 * @property int $id
 * @property int $loan_type
 * @property string $date
 * @property int $account_id
 * @property int $user_id
 * @property int $amount
 * @property int $Installment_count
 * @property string $first_installment
 * @property int $remain
 * @property string $description
 * @property int $status
 * @property string $modified_date
 * @property int $last_modified_by
 *
 * @property Account $account
 * @property User $lastModifiedBy
 * @property User $user
 * @property LoanType $loanType
 * @property LoanCosts[] $loanCosts
 * @property LoanPayment[] $loanPayments
 */
class LoanEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_type', 'date', 'account_id', 'user_id', 'amount', 'Installment_count', 'first_installment', 'remain', 'status', 'modified_date', 'last_modified_by'], 'required'],
            [['loan_type', 'account_id', 'user_id', 'amount', 'Installment_count', 'remain', 'status', 'last_modified_by'], 'integer'],
            [['date', 'first_installment', 'modified_date'], 'safe'],
            [['description'], 'string'],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['last_modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_modified_by' => 'user_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['loan_type'], 'exist', 'skipOnError' => true, 'targetClass' => LoanType::className(), 'targetAttribute' => ['loan_type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_type' => 'Loan Type',
            'date' => 'Date',
            'account_id' => 'Account ID',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'Installment_count' => 'Installment Count',
            'first_installment' => 'First Installment',
            'remain' => 'Remain',
            'description' => 'Description',
            'status' => 'Status',
            'modified_date' => 'Modified Date',
            'last_modified_by' => 'Last Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastModifiedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'last_modified_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoanType()
    {
        return $this->hasOne(LoanType::className(), ['id' => 'loan_type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoanCosts()
    {
        return $this->hasMany(LoanCostsEntity::className(), ['loan_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoanPayments()
    {
        return $this->hasMany(LoanPayment::className(), ['loan_id' => 'id']);
    }
}
