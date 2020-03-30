<?php

namespace app\models\entities;

use Yii;
use app\models\Loan;
use app\models\Costs;

/**
 * This is the model class for table "loan_costs".
 *
 * @property int $id
 * @property int $loan_id
 * @property int $cost_id
 * @property int $count
 *
 * @property Loan $loan
 * @property Costs $cost
 */
class LoanCostsEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_costs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', 'cost_id'], 'required'],
            [['loan_id', 'cost_id', 'count'], 'integer'],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loan::className(), 'targetAttribute' => ['loan_id' => 'id']],
            [['cost_id'], 'exist', 'skipOnError' => true, 'targetClass' => Costs::className(), 'targetAttribute' => ['cost_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => 'Loan ID',
            'cost_id' => 'Cost ID',
            'count' => 'Count'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoan()
    {
        return $this->hasOne(Loan::className(), ['id' => 'loan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCost()
    {
        return $this->hasOne(Costs::className(), ['id' => 'cost_id']);
    }
}
