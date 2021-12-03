<?php
namespace App\Http\Libs\Api;

use Illuminate\Support\Facades\Storage;

class Exchange extends Base
{
    public function __construct($token = null)
    {
        if (is_null($token))
            $token = config('app.host_token');
        parent::__construct($token);
    }

    public function setData($params = [])
    {
        $response = $this->httpClient->post(
            config('app.api_host') . '/api/v1',
            $this->prepare_params('data@setData', $params)
        );
        if ($response->ok()) {
            $result = $response->json();
            if (!empty($result['result']['success']) && ($result['result']['success'] == true)) {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    public function saveFile($file, $name){

        $response = $this->httpImageClient->attach(
            'file', file_get_contents($file), $name
        )->post(
            config('app.api_host') . '/api/v1/setData'
        );
        if ($response->ok()) {
            $result = $response->json();
            if(!empty($result['success']) && ($result['success'] == true)){
                return $result['path'];
            }
            return false;
        }
        return false;
    }

    public function getFile($params = []){
        $response = $this->httpImageClient->get(
            config('app.payment_system_host') . '/api/v1/get/file',
            $params
        );
        if ($response->ok()) {
            return $response->body();
        }
        return false;
    }

    public function getPath($params = []){
        $response = $this->httpImageClient->get(
            config('app.payment_system_host') . '/api/v1/get/path',
            $params
        );
        if ($response->ok()) {
            $result = $response->json();
            if(!empty($result['success']) && ($result['success'] == true)){
                return $result['path'];
            }
            return false;
        }
        return false;
    }


    public function getTraderDealStatus()
    {
        $response = $this->httpClient->post(
            config('app.payment_system_host') . '/api/v1',
            $this->prepare_params('trader@getTraderDealStatus')
        );
        if ($response->ok()) {
            $result = $response->json();
            if (!empty($result['result']['success']) && ($result['result']['success'] == true)) {
                return $result['result']['statuses'];
            }
        } else {
            return false;
        }
        return false;
    }

    public function getTraderDealAvalableStatus()
    {
        $response = $this->httpClient->post(
            config('app.payment_system_host') . '/api/v1',
            $this->prepare_params('trader@getTraderDealAvalableStatus')
        );
        if ($response->ok()) {
            $result = $response->json();
            if (!empty($result['result']['success']) && ($result['result']['success'] == true)) {
                return $result['result']['statuses'];
            }
        } else {
            return false;
        }
        return false;
    }

    public function getCustomerType()
    {
        $response = $this->httpClient->post(
            config('app.payment_system_host') . '/api/v1',
            $this->prepare_params('trader@getCustomerType')
        );
        if ($response->ok()) {
            $result = $response->json();
            if (!empty($result['result']['success']) && ($result['result']['success'] == true)) {
                return $result['result']['types'];
            }
        } else {
            return false;
        
        return false;
    }


}
