<?php

namespace magicalella\wswordpress;

use Yii;
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
    
    //for Woocommerce
    public $consumer_key;
    public $consumer_secret;
    
    const STATUS_SUCCESS = true;
    const STATUS_ERROR = false;
    const INVALID_ARGUMENT = '400';//messaggio non valido anche per lunghezza
    const ELEMENTO_NON_TROVATO = '404';//elemento non trovato

    const THIRD_PARTY_AUTH_ERROR = '401';//APNs certificate or web push auth key was invalid or missing.
    const SENDER_ID_MISMATCH = '403';//stai sbagliando credenziali
    const QUOTA_EXCEEDED = '429';//superamento numero messaggi inviabili
    const INTERNAL = '500';//errore server firebase 
    const UNAVAILABLE = '503';//server overload non raggiungibile


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
     * @param string $method
     * @param array $data
     * @param $woocommerce true/false
     * @return response []
     *      status 0/1 success o error
     *      message
     *      data dati della risposta formato json
     */
    public function call($call, $method = 'GET', $data = [] , $woocommerce = false)
    {
        $status = SELF::STATUS_SUCCESS;
        $message = '';
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
        $response = $this->curl($this->endpoint.$call, $json, $method ,$woocommerce);
        $data_response = json_decode($response['data']);
        /* ERRORE in $data_response = [
        *    code se errore
        *    message se errore
        *    data [
        *        status / array dati
        *    ]
        *]
        * OK $data_response = [
        *    array dati
        *]
        */
        if(is_array($data_response)){
            if(!empty($data_response) && key_exists('code', $data_response)){
            $status = SELF::STATUS_ERROR;
            $message = $data_response->code.' - '.$data_response->data->status.' - '.$data_response->message;
            Yii::error(sprintf('ERRORE WP:  errore chiamata WP: %s  ', $message), __METHOD__);
    }
        }else{
            $message =  $response['data'];
            $status = SELF::STATUS_ERROR;
            Yii::error(sprintf('ERRORE WP:  errore chiamata WP: %s  ', $message), __METHOD__);
        }

        return $response = [
            'data' => $data_response,
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Do request by CURL
     * @param $url ex: https://www.mysite.com/wp-json
     * @param $data
     * @param $method
     * @param $woocommerce true/false
     * @return response []
     *      status 0/1 success o error
     *      message
     *      data dati della risposta formato json 
     */
    private function curl($url, $data, $method = 'GET',$woocommerce = false)
    {
        $response = [];
        $status = self::STATUS_SUCCESS;
        $header = [
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8',
            'User-Agent: Override'
        ];
        if($woocommerce){
            $base64 = base64_encode($this->consumer_key.':'.$this->consumer_secret);
            $wooheader = ['Authorization: Basic '.$base64];
            $header = array_merge($header,$wooheader);
        }
       
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if(!empty($data))
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header
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
