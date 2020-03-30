<?php

namespace app\models\entities;

use Yii;
use app\models\User;
use app\models\Account;

/**
 * This is the model class for table "account_type".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $charge_rate
 * @property string $modified_date
 * @property int $last_modified_by
 *
 * @property Account[] $accounts
 * @property User $lastModifiedBy
 */
class AccountTypeEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'charge_rate', 'modified_date', 'last_modified_by'], 'required'],
            [['charge_rate', 'last_modified_by'], 'integer'],
            [['modified_date'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 500],
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
            'name' => 'Name',
            'description' => 'Description',
            'charge_rate' => 'Charge Rate',
            'modified_date' => 'Modified Date',
            'last_modified_by' => 'Last Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['type' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastModifiedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'last_modified_by']);
    }
}
