<?php

namespace app\models\entities;

use Yii;
use app\models\User;
use app\models\Account;

/**
 * This is the model class for table "account_detail".
 *
 * @property int $id
 * @property int $account_id
 * @property int $amount
 * @property int $type
 * @property string $description
 * @property string $date 
 * @property int $user_id
 * @property int $reference_id
 * @property int $returned
 * @property string $modified_date
 * @property int $last_modified_by
 *
 * @property User $user
 * @property User $lastModifiedBy
 * @property Account $account
 */
class AccountDetailEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['account_id', 'amount', 'type', 'date', 'modified_date', 'last_modified_by'], 'required'],
            [['account_id', 'amount', 'type', 'user_id', 'reference_id', 'returned', 'last_modified_by'], 'integer'],
            [['date', 'modified_date'], 'safe'],
            [['description'], 'string', 'max' => 500],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'user_id']],
            [['last_modified_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_modified_by' => 'user_id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'amount' => 'Amount',
            'type' => 'Type',
            'description' => 'Description',
            'date' => 'Date',
            'user_id' => 'User ID',
            'reference_id' => 'Reference ID',
            'returned' => 'Returned',
            'modified_date' => 'Modified Date',
            'last_modified_by' => 'Last Modified By',
        ];
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
    public function getLastModifiedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'last_modified_by']);
    }
	 
   /** 
    * @return \yii\db\ActiveQuery 
    */ 
    public function getAccount() 
    { 
        return $this->hasOne(Account::className(), ['id' => 'account_id']); 
    }
}
