<?php
/**
 *  User Model
 *
 *  Customized Model for Application user access.
 *
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

use app\models\entities\UserEntity;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\notification\NotifiableTrait;

class User extends UserEntity implements \yii\web\IdentityInterface, \app\modules\notification\NotifiableInterface
{
    use NotifiableTrait;
    
    const STAT_ACTIVE = 1;
    const STAT_DEACTIVE = 2;

    public $authKey;
    public $accessToken;
    //for gridview
    public $role;
    public $segment;

    public $passwordConfirm;
    public $initialPassword;

    public function rules() {
        $parent_rules = parent::rules();
        $parent_rules[] = [['password', 'passwordConfirm'], 'required', 'on'=>'insert'];
        $parent_rules[] = ['password', 'compare', 'compareAttribute'=>'passwordConfirm', 'message'=>"تکرار کلمه عبور با کلمه عبور یکسان نیست" ];
        $parent_rules[] = ['passwordConfirm', 'compare', 'compareAttribute'=>'password', 'message'=>"تکرار کلمه عبور با کلمه عبور یکسان نیست" ];

        return $parent_rules;
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['insert'] = $scenarios['update'] = ['user_group_id', 'seg_id', 'segment_master', 'email', 'full_name','password', 'passwordConfirm', 'remarks', 'socials', 'status'];
        $scenarios['search'] = ['user_id','segment','segment_master','role','email', 'full_name','status'];

        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'شناسه',
            'user_group_id' => 'گروه',
            'email' => 'ایمیل',
            'seg_id' => 'صندوق',
            'segment_master' => 'کاربر سیستمی',
            'full_name' => 'نام کامل',
            'password' => 'کلمه عبور',
            'remarks' => 'توضیح',
            'socials' => 'شبکه های اجتماعی',
            'status' => 'وضعیت',
        ];
    }

    public function routeNotificationForMail() 
    {
         return $this->email;
    }

    public static function getStatuses(){
        return [
            self::STAT_ACTIVE => 'فعال',
            self::STAT_DEACTIVE => 'غیر فعال'
        ];
    }

    public static function getSocialNames(){
        return [
            'telegram' => 'تلگرام',
            'whatsapp' => 'واتسپ',
            'sms' => 'اس ام اس'
        ];
    }

    public function keepPass()
    {
        //reset the password to null because we don't want the hash to be shown.
        $this->initialPassword = $this->password;
        $this->password = null;
    }

    public function afterFind() {
        parent::afterFind();
        $this->socials = unserialize($this->socials);
        return $this;
    }

    public function beforeSave($insert)
    {
        // in this case, we will use the old hashed password.
        if(empty($this->password) && empty($this->passwordConfirm) && !empty($this->initialPassword))
            $this->password=$this->passwordConfirm=$this->initialPassword;

        return parent::beforeSave($insert);
    }

    public function saveModel()
    {
        if(!empty($this->password) && !empty($this->passwordConfirm))
        {
            $this->password = sha1($this->password);
            $this->passwordConfirm = sha1($this->passwordConfirm);
        }
        $this->socials = serialize(\Yii::$app->request->post()['socials']);

        if(!$this->save())
            return false;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(['user_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //return self::findOne(['accessToken' => $token]);
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return self::findOne(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return $this->password === sha1($password);
    }

    public static function getUsers(){
        return ArrayHelper::map(User::find()->where(['seg_id'=>\Yii::$app->user->getSegment()])->all(), 'user_id','full_name');
    }

    public function search($params)
    {
        $query = User::find()->joinWith(['userGroup','seg']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'user_id' => SORT_DESC,
                ],
                'attributes' => [
                    'user_id',
                    'segment' => [
                        'asc' => ['seg.name' => SORT_ASC],
                        'desc' => ['seg.name' => SORT_DESC],
                    ],
                    'segment_master',
                    'role' => [
                        'asc' => ['userGroup.role' => SORT_ASC],
                        'desc' => ['userGroup.role' => SORT_DESC],
                    ],
                    'email',
                    'full_name',
                    'user.status'
                ],
            ],
        ]);

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'seg_id' => $this->seg_id,
            'segment_master' => $this->segment_master,
            'user.user_group_id' => $this->role,
            'user.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
              ->andFilterWhere(['like', 'full_name', $this->full_name]);

        return $dataProvider;
    }
}