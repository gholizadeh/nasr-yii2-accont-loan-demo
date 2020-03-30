<?php

namespace app\models\entities;

use Yii;
use app\models\User;

/**
 * This is the model class for table "loan_type".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $modified_date
 * @property int $last_modified_by
 *
 * @property User $lastModifiedBy
 */
class LoanTypeEntity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'loan_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'modified_date', 'last_modified_by'], 'required'],
            [['modified_date'], 'safe'],
            [['last_modified_by'], 'integer'],
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
            'modified_date' => 'Modified Date',
            'last_modified_by' => 'Last Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastModifiedBy()
    {
        return $this->hasOne(User::className(), ['user_id' => 'last_modified_by']);
    }
}
