<?php
namespace app\components;

use yii\db\ActiveRecord;

class ChangeLogBehavior extends \cranky4\changeLogBehavior\ChangeLogBehavior{

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'addLog',
            ActiveRecord::EVENT_BEFORE_DELETE => 'addDeleteLog',
        ];
    }
}