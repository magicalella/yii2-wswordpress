<?php

namespace magicalella\wswordpress;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Exception;

/**
 * Class Wswordpress
 * Wswordpress component
 * @package magicalella\wswordpress
 *
 * @author Raffaella Lollini
 */
class Wswordpress extends Component
{

    /**
     * @var string Random pice of string
     */
    //public $apiKey;
    
    /**
     * @var string
     * https://miosito/wp-json/
     */
    public $endpoint;
    
    /**
     * @var string metodo della chiamata POST - GET - DELETE - PATCH
     */
    public $method;
    
    const STATUS_SUCCESS = true;
    const STATUS_ERROR = false;


    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        

        // if (!$this->apiKey) {
        //     throw new InvalidConfigException('$apiKey not set');
        // }

        if (!$this->endpoint) {
            throw new InvalidConfigException('$endpoint not set');
        }
        
        if (!$this->method) {
            throw new InvalidConfigException('$owner not set');
        }

        parent::init();
    }

    /**
     * Call Wswordpress function
     * @param string $call Name of API function to call
     * @param array $data
     * @return response []
     *      status 0/1 success o error
     *      message
     *      data dati della risposta formato json
     */
    public function call($call, $method = 'GET', $data = [])
    {
        // $data = array_merge(
        //     array(
        //         'apiKey' => $this->apiKey,
        //         'owner' => $this->owner,
        //         'requestTime' => time(),
        //         'sha' => sha1($this->apiKey . $this->clientId . $this->apiSecret),
        //     ),
        //     $data
        // );
        $json = json_encode($data);
        //print_r($json);
        $response = $this->curl($this->endpoint.$call, $json, $method);
        $response['data'] = json_decode($response['data']);
        return $response;
    }

    /**
     * Do request by CURL
     * @param $url ex: https://api.connectif.cloud/purchases/
     * @param $data
     * @param $method
     * @return response []
     *      status 0/1 success o error
     *      message
     *      data dati della risposta formato json 
     */
    private function curl($url, $data, $method = 'GET')
    {
        $response = [];
        $status = self::STATUS_SUCCESS;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: application/json, application/json',
                'Content-Type: application/json;charset=UTF-8'
                //'Authorization: apiKey '.$this->apiKey
                //'Content-Length: ' . strlen($data)
            )
        );
        $dati = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $error = curl_error($ch);
        if($error){
            $status = self::STATUS_ERROR;
        }
        curl_close($ch);
        $response = [
            'status' => $status,
            'data' => $dati
        ];
        return $response;
    }
}

/**
 * @package BridgeWP
 */
class WswordpressException extends Exception
{
}
