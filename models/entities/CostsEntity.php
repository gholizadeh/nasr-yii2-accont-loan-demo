<?php

namespace app\models\entities;

use Yii;

/**
 * This is the model class for table "costs".
 *
 * @property int $id
 * @property string $name
 * @property int $amount
 * @property string $description
 * @property int $status
 *
 * @property LoanCosts[] $loanCosts
 */
class CostsEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'costs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'amount', 'status'], 'required'],
            [['amount', 'status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 500],
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
            'amount' => 'Amount',
            'description' => 'Description',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoanCosts()
    {
        return $this->hasMany(LoanCostsEntity::className(), ['cost_id' => 'id']);
    }
}
