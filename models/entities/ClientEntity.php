<?php

namespace app\models\entities;

use Yii;
use app\models\Account;

/**
 * This is the model class for table "client".
 *
 * @property int $client_id
 * @property string $name
 * @property string $cellphone
 * @property string $remarks
 *
 * @property Account[] $accounts
 * @property ClientSegmentEntity[] $clientSegments
 */
class ClientEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['cellphone'], 'string', 'max' => 11],
            [['remarks'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Client ID',
            'name' => 'Name',
            'cellphone' => 'Cellphone',
            'remarks' => 'Remarks',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccounts()
    {
        return $this->hasMany(Account::className(), ['client_id' => 'client_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientSegments()
    {
        return $this->hasMany(ClientSegmentEntity::className(), ['client_id' => 'client_id']);
    }
}
