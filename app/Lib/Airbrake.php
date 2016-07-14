<?php


namespace App\Lib;


use Airbrake\Notifier;
use App\Models\SelfLog;
use App\Models\WebApp;

class Airbrake
{

  public static function send(array $input)
  {

      $app = WebApp::find($input['web_app_id']);

      if(!$app){
          return false;
      }



      $airbrakeCon = new Notifier(['projectId' => '126972', 'projectKey' => '1028a99cb409ee35ab22e53966e457c8']);


      $response = $airbrakeCon->sendNotice([
            "errors" => [[
                "type" =>  $app->app_name . ' - Error: ' . $input['msg'],
                "message" => $app->app_name . ' - Error: ' . $input['msg']
                ]
            ],
      ]);


      return true;

  }

}