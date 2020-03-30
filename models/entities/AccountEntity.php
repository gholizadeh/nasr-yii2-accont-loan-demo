<?php

namespace app\models\entities;

use Yii;
use app\models\Client;
use app\models\Segment;
use app\models\AccountType;
use app\models\Loan;
use app\models\User;
use app\models\AccountDetail;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property int $seg_id
 * @property int $client_id
 * @property int $remain_debit
 * @property int $type
 * @property string $name
 * @property string $create_date
 * @property string|null $modified_date
 * @property int|null $last_modified_by
 * @property int $status
 * @property string $settlement_til
 *
 * @property AccountDetail[] $accountDetails
 * @property Client $client
 * @property User $lastModifiedBy
 * @property Loan $loan
 * @property Segment $seg
 * @property AccountType $AccountType
 */
class AccountEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['seg_id', 'client_id', 'type', 'name', 'create_date', 'settlement_til'], 'required'],
            [['seg_id', 'client_id', 'remain_debit', 'type', 'last_modified_by', 'status'], 'integer'],
            [['create_date', 'modified_date', 'settlement_til'], 'safe'],
            [['name'], 'string', 'max' => 50],
            [['seg_id'], 'exist', 'skipOnError' => true, 'targetClass' => Segment::className(), 'targetAttribute' => ['seg_id' => 'seg_id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'client_id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => AccountType::className(), 'targetAttribute' => ['type' => 'id']],
            [['last_modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_modified_by' => 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'seg_id' => 'Seg ID',
            'client_id' => 'Client ID',
            'remain_debit' => 'Remain Debit',
            'type' => 'Type',
            'name' => 'Name',
            'create_date' => 'Create Date', 
            'modified_date' => 'Modified Date', 
            'last_modified_by' => 'Last Modified By', 
            'status' => 'Status',
            'settlement_til' => 'Settlement Til',
        ];
    }

   /**
    * Gets query for [[AccountDetails]]. 
    * 
    * @return \yii\db\ActiveQuery 
    */ 
    public function getAccountDetails() 
    { 
       return $this->hasMany(AccountDetail::className(), ['account_id' => 'id']); 
    } 

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeg()
    {
        return $this->hasOne(Segment::className(), ['seg_id' => 'seg_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccountType()
    {
        return $this->hasOne(AccountType::className(), ['id' => 'type']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['account_id' => 'id']);
    }

	/** 
     * Gets query for [[LastModifiedBy]]. 
     * 
     * @return \yii\db\ActiveQuery 
     */ 
    public function getLastModifiedBy() 
    { 
        return $this->hasOne(User::className(), ['user_id' => 'last_modified_by']); 
    }
}
