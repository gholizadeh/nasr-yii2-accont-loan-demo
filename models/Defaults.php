<?php
/**
 *  Default Values
 * @author: S.Gholizadeh. <gholizade.saeed@yahoo.com>
 */
namespace app\models;

class Defaults
{    
    const STAT_ACTIVE = 1;
    const STAT_DEACTIVE = 2;

    const YES = 1;
    const NO = 0;

    public static function getStatuses(){
        return [
            self::STAT_ACTIVE => 'فعال',
            self::STAT_DEACTIVE => 'غیر فعال'
        ];
    }

    public static function getYesNo(){
        return [
            self::YES => 'بله',
            self::NO => 'خیر'
        ];
    }

}