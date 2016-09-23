<?php
/**
 * Created by PhpStorm.
 * User: chrisendcliffe
 * Date: 02/09/2016
 * Time: 09:25
 */

namespace App\Lib;
use Monolog\Logger as Log;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class MonitorLogger
{

    public static function debug($message, $info = array())
    {
        $logger = self::MonoLoggerInstance();

        $logger->addDebug($message, $info);
    }

    public static function error($message, $info = array())
    {
        $logger = self::MonoLoggerInstance();

        $logger->addError($message, $info);
    }


    public static function MonoLoggerInstance()
    {

        $logger = new Log('monitor-logger');

        $logger->pushHandler(new StreamHandler(storage_path('logs/monitor-logger.log')));

        $logger->pushHandler(new FirePHPHandler());

        return $logger;

    }


}