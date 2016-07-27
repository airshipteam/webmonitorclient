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
    public function __construct($appId)
    {
        $this->appId = $appId;
    }


    public function start()
    {
        $response = $this->post('/pings/start', [
            'app_id' => $this->appId,
            'time_sent' => date('Y-m-d H:i:s', time())
        ]);

        $decodedResponse = json_decode($response);

        if(!$decodedResponse)
        {
            return false;
        }

        $this->runId = $decodedResponse->run_id;

        return true;

    }

    public function end()
    {
        $response = $this->post('/pings/end', [
            'app_id' => $this->appId,
            'run_id' => $this->runId,
            'time_sent' => date('Y-m-d H:i:s', time())
        ]);

        $decodedResponse = json_decode($response);

        if(!$decodedResponse)
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

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        try {
            $result =  curl_exec($ch);

        }catch(\Exception $e)
        {
            $result = null;
        }
        curl_close($ch);

        return $result;

    }


}