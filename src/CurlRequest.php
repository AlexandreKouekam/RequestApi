<?php

namespace ATEKA;

class CurlRequest
{
    protected $ch;

    protected $response;

    public function __construct($userOrApiKey = null, $password = null)
    {
        $this->ch = \curl_init();
        if ($userOrApiKey !== null && $password !== null){
            curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $userOrApiKey . ':' . $password);
        } elseif ($userOrApiKey !== null) {
            curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $userOrApiKey . ':' . rand(10000,90000));
        }
    }


    public function prepare($url, $method = null, $data = '')
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_COOKIESESSION, 1);

        if (strtoupper($method) === 'POST'){
            curl_setopt($this->ch, CURLOPT_POST, 1);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);

        } elseif (strtoupper($method) === 'PUT') {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);

        } elseif (strtoupper($method) === 'DELETE') {
            curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        }
    }

    public function setContent($type)
    {
        if (strtolower($type) === 'json') {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    ]
            );
        } elseif (strtolower($type) === 'xml'){
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: text/xml',
                ]
            );
        }
    }

    public function getResponse()
    {

        return $this->response;
    }

    public function execute()
    {
        $result = curl_exec($this->ch);
        $this->response = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        curl_close($this->ch);

        return $result;
    }
}
