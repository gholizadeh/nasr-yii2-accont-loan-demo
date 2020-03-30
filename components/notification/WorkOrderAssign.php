<?php
namespace app\components\notification;

use app\modules\notification\models\Notification;
use app\modules\notification\channels\TelegramChannel;
use app\models\WorkOrder;

class WorkOrderAssign extends Notification
{    
    private $wo;

    public function init(){
        $this->wo = WorkOrder::findOne($this->params['wo_id']);
    }

    public function exportForScreen(){
        return \Yii::createObject([
            'class' => '\app\modules\notification\messages\ScreenMessage',
            'subject' => "WorkOrder assigned",
            'body' => "You have been assigned to ".Html::a('WorkOrder #'.$this->wo->wo_code, ['/work-order/update', 'id' => $this->wo->work_order_id]).". click on the link to update the WorkOrder",
            'bold' => false,
        ]);
    }
    
    public function exportForMail() {
        return \Yii::createObject([
           'class' => '\app\modules\notification\messages\GeneralMessage',
           'subject' => "WorkOrder assigned",
           'channelParams' => [
                'view' => ['html' => 'work-order'],
                'viewData' => [
                    'model' => $this->wo,
                ]
            ]
        ]);
    }
    
    public function exportForTelegram()
    {
        return \Yii::createObject([
            'class' => '\app\modules\notification\messages\GeneralMessage',
            'body' => "You have been assigned to ".Html::a('WorkOrder #'.$this->wo->wo_code, ['/work-order/update', 'id' => $this->wo->work_order_id]).". click on the link to update the WorkOrder"
        ]);
    }
}