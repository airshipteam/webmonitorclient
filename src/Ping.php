<?php

namespace airshipwebservices\webmonitorclient;

class Ping
{

    protected $appId;

    protected $runId;

    /**
     * Ping constructor.
     * @param $appId
     */
    public function __construct($appId = FALSE)
    {
        $this->appId = $appId;
    }


    public function start()
    {
        $response = $this->post('/pings/start', [
            'app_id' => $this->appId,
            'time_sent' => date('Y-m-d H:i:s', time())
        ]);


        if(!$response)
        {
            return false;
        }

        $this->runId = $response->run_id;

        return true;

    }

    public function end()
    {

        if(!$this->runId)
        {
            return false;
        }

        $response = $this->post('/pings/end', [
            'app_id' => $this->appId,
            'run_id' => $this->runId,
            'time_sent' => date('Y-m-d H:i:s', time())
        ]);

        if(!$response)
        {
            return false;
        }
        return true;
    }

    private function url()
    {
        include( 'Config.php' );

        return $config['web_monitor_host'];

    }


    private function post($endpoint, array $params)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url(). $endpoint);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = $this->makeCall($ch);

        return $result;

    }

    private function makeCall($ch)
    {
        try {
            $response =  curl_exec($ch);

        }catch(\Exception $e)
        {
            return null;
        }

        $info = curl_getinfo($ch);

        curl_close($ch);

        if($info['http_code'] != 200)
        {
            return null;
        }

        return json_decode($response);


    }


}