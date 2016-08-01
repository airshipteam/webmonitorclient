<?php
/**
 * Created by PhpStorm.
 * User: chrisendcliffe
 * Date: 26/07/2016
 * Time: 15:02
 */

namespace App\Lib;


use App\Models\WebApp;
use App\Ping;
use Airbrake\Notifier;

class Pingdom
{


    public static function getUnfinishedRuns()
    {

        return Ping::where('status', 'pending')->where('type', 'start')->get();

    }

    public static function getRunsExceedingMaxExecutionTime()
    {

        $runs = self::getUnfinishedRuns();

        $timedOutRuns = [];

        foreach($runs as $run)
        {
            $app = WebApp::find($run->web_app_id);

            if(!$app)
            {
                return false;
            }



            if(self::hasRunExceededMaxExecutionTime($app, $run))
            {
               $timedOutRuns[] = $run;
            }
        }

        return $timedOutRuns;

    }

    public static function sendAirbrakeAlert(WebApp $app, Ping $startPing)
    {

        self::airbrake($app->app_name . ' has exceeded its max execution time', 'Run ID = ' . $startPing->run_id .' / Max execution time ' . $app->max_execution_time_seconds . ' secs');

    }

    public static function airbrake($type, $message)
    {
        $airbrakeCon = new Notifier(['projectId' => '126972', 'projectKey' => '1028a99cb409ee35ab22e53966e457c8']);

        $airbrakeCon->sendNotice([
            "errors" => [
                [
                    "type" => $type,
                    "message" => $message
                ]
            ],
        ]);

    }

    public static function hasRunExceededMaxExecutionTime(WebApp $app, Ping $startPing)
    {
        $expectedCompletion = self::expectedCompletion($app, $startPing);

        if(time() > $expectedCompletion)
        {
            return true;
        }

        return false;
    }

    public static function expectedCompletion(WebApp $app, Ping $startPing)
    {
        $timeSent = strtotime($startPing->created_at);

        return  $timeSent + $app->max_execution_time_seconds;
    }

    public static function createRunID(WebApp $webApp)
    {

        return hash('sha256', $webApp->id.$webApp->app_name.time());

    }

    public static function completeRun(array $input)
    {

        $startPing = Ping::where('run_id', $input['run_id'])->where('type', 'start')->first();

        if(!$startPing)
        {
            return false;
        }


        if(self::hasRunExceededMaxExecutionTime(WebApp::find($startPing->web_app_id), $startPing))
        {
            self::markAsTimedOut($startPing, $input['time_sent']);

            return true;
        }

        if($startPing->status == 'pending')
        {
            self::markAsComplete($startPing, $input['time_sent']);

            return true;
        }

        if($startPing->status == 'exceeded_execution_time' || $startPing->status == 'failed')
        {

            self::markAsTimedOut($startPing, $input['time_sent']);

            return true;
        }

        return false;


    }

    public static function markAsComplete(Ping $startPing, $timeCompleted)
    {

        Ping::create([
            'web_app_id' => $startPing->web_app_id,
            'type' => 'end',
            'status' => 'complete',
            'run_id' => $startPing->run_id,
            'time_sent' => $timeCompleted
        ]);

        $startPing->status = 'complete';

        $startPing->save();

        return true;
    }

    public static function markAsTimedOut(Ping $startPing, $timeCompleted)
    {
        Ping::create([
            'web_app_id' => $startPing->web_app_id,
            'type' => 'end',
            'status' => 'exceeded_execution_time',
            'run_id' => $startPing->run_id,
            'time_sent' => $timeCompleted
        ]);

        return true;
    }

    public static function getLastStartPing(WebApp $app)
    {
        return Ping::where('type', 'start')->where('web_app_id', $app->id)->orderBy('time_sent', 'desc')->first();
    }

    public static function hasAppNotSentAPing(WebApp $app)
    {

        $runSchedule = $app->runSchedule;

        if($runSchedule->id === 0)
        {
            return false;
        }

        $requiredInterval = $runSchedule->getAliveInterval();

        $lastPing = self::getLastStartPing($app);


        if(!$lastPing)
        {
            return false;
        }

        $secondsSinceLastPing = time() - strtotime($lastPing->time_sent);

        if($secondsSinceLastPing > $requiredInterval)
        {
            return true;
        }

        return false;

    }
}