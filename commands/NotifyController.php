<?php
namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command looks at notify queue and send messages to users.
 *
 * @author Saeed Gholizadeh <gholizade.saeed@yahoo.com>
 */
class NotifyController extends Controller
{

    public function actionIndex(){
        
    }

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
    public function actionIndex($message = 'hello')
    {
        echo $message . "\n";
        return ExitCode::OK;
    }
    */
}
