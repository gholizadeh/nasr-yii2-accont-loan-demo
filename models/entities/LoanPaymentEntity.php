<?php

namespace app\models\entities;

use Yii;
use app\models\User;
use app\models\Loan;

/**
 * This is the model class for table "loan_payment".
 *
 * @property int $id
 * @property int $loan_id
 * @property int $amount
 * @property string $date
 * @property string $modified_date
 * @property int $last_modified_by
 * @property int $status
 *
 * @property Loan $loan
 * @property User $lastModifiedBy
 */
class LoanPaymentEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['loan_id', 'amount', 'date', 'modified_date', 'last_modified_by', 'status'], 'required'],
            [['loan_id', 'amount', 'last_modified_by', 'status'], 'integer'],
            [['date', 'modified_date'], 'safe'],
            [['loan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Loan::className(), 'targetAttribute' => ['loan_id' => 'id']],
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
            'loan_id' => 'Loan ID',
            'amount' => 'Amount',
            'date' => 'Date',
            'modified_date' => 'Modified Date',
            'last_modified_by' => 'Last Modified By',
            'status' => 'Status'
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
    public function getLastModifiedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'last_modified_by']);
    }
}
