<?php
/*
* ニフティクラウドmobile backendにpush通知を登録する
* フレームワークはlaravel
* 通知に設定するパラメータ
* http://mb.cloud.nifty.com/doc/current/rest/push/pushRegistration.html
* sendPush()の$bodyに上記URLのパラメータを設定して実行する
*/
namespace App\Libs;

use Config;
use DateTime;
use DateTimeZone;
use Exception;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class PushNotification
{
    private $request_params  = null;
    private $request_url     = null;
    private $response_status = null;
    private $is_error        = true;
    private $client_key      = null;
    private $settings        = null;

    public function __construct()
    {
        $this->log = new Logger('pushLog');
        $stream = new StreamHandler(storage_path('logs/debug/push.log'));
        $formatter = new LineFormatter(null, null, true);
        $stream->setFormatter($formatter);
        $this->log->pushHandler($stream);

        // push通知のconfig
        $this->settings = [
            'X-NCMB-Application-Key' => 'アプリケーションキー',
            'client_key'             => 'クライアントキー',
            'api_url'                => 'https://mb.api.cloud.nifty.com/2013-09-01/',
            'SignatureMethod'        => 'HmacSHA256',
            'SignatureVersion'       => 2,
        ];
        $this->client_key = $this->settings['client_key'];
    }

    public function sendPush($body, $method = 'push', $query=[])
    {
        $this->is_error = true ;
        $this->response_status = null;
        $this->request_params = null;
        $this->request_url = null;

        // 日付形式変換
        if (!empty($body['deliveryTime'])) {
            $t = new DateTime($body['deliveryTime']);
            $t->setTimeZone(new DateTimeZone('UTC'));
            $body['deliveryTime'] = ['__type' => 'Date', 'iso' => $t->format('Y-m-d\TH:i:00.000\Z')];
        }

        // 開発環境でmobilebackendに登録するのを回避する
        // if (Config::get('const.push_notification_flag') === false) {
        //     $this->log->addDebug(var_export($body, true));
        //     $this->is_error = false;
        //     return true;
        // }

        $sign_date = $this->sign_date();
        $this->request_params = array_merge(
            [
                'X-NCMB-Application-Key' => $this->settings['X-NCMB-Application-Key'],
                'SignatureVersion' => $this->settings['SignatureVersion'],
                'SignatureMethod' => $this->settings['SignatureMethod'],
                'X-NCMB-Timestamp' => $sign_date,
            ],
            $query
        );

        $this->request_url = "{$this->settings['api_url']}{$method}";
        $sign = $this->sign();
        $headers = [
            'Content-type: application/json',
            "X-NCMB-Application-Key: {$this->settings['X-NCMB-Application-Key']}",
            "X-NCMB-Timestamp: {$sign_date}",
            "X-NCMB-Signature: {$sign}"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $this->request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, false) ;
        curl_setopt($ch, CURLOPT_POST, true) ;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        $response = curl_exec($ch);
        return $this->_parseResponse($response, $ch);
    }

    public function isError()
    {
        return $this->is_error;
    }

    private function _parseResponse($response, $ch)
    {
        if ( $response === false ) {
            return false ;
        }
        try {
            $this->response_status  = curl_getinfo($ch);
            if ( (int)((int)$this->response_status['http_code']/100) != 2 ) {
                $result = $this->analyze_response($response);
            } else {
                $result = $this->analyze_response($response);
                $this->is_error = false ;
            }
        } catch (Exception $e) {
            $result = [
                'code' => $e->getCode(),
                'error' => 'Respose parse error.'
            ];
        }
        curl_close($ch);
        return $result;
    }

    public function getResponseStatus()
    {
        return $this->response_status;
    }

    private function analyze_response($response)
    {
        list($response_header, $response_body) = explode("\r\n\r\n", $response, 2);
        $result = json_decode($response_body);
        if (($error = json_last_error()) != JSON_ERROR_NONE) {
            throw new Exception("parse error", $error);
        }
        return $result;
    }

    private function create_query_string($params)
    {
        $param_string = "";
        foreach ($params as $key => $value) {
            if ( strlen($param_string) > 0 )
            $param_string .= "&";
            $param_string .= $key . '=' . $value;
        }
        return $param_string;
    }

    private function sign()
    {
        $params = $this->request_params;
        uksort($params, 'strnatcmp');
        $url_hash = parse_url($this->request_url);
        $string_to_sign = "POST\n{$url_hash['host']}\n{$url_hash['path']}\n";
        $string_to_sign .= $this->create_query_string($params);
        $method = strtolower(substr($params['SignatureMethod'], 4));
        return base64_encode(hash_hmac($method, $string_to_sign, $this->client_key, true));
    }

    private function sign_date($time = 'now')
    {
        return date('Y-m-d\TH:i:s\Z', strtotime($time));
    }
}
