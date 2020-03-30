<?php
namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class TimeLogBehavior extends Behavior{

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
        ];
    }

    public function beforeValidate($event)
    {
        $this->owner->modified_date = new Expression('UTC_TIMESTAMP()');
        if($this->owner->hasProperty('last_modified_by'))
            $this->owner->last_modified_by = Yii::$app->user->id;
    }
}