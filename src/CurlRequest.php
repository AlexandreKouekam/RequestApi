<?php

namespace ATEKA;

class CurlRequest
{
    protected $ch;
    protected $responseHeader;
    protected $responseBody;
    protected $responseCode;

    public function __construct($userOrApiKey = null, $password = null)
    {
        $this->ch = \curl_init();
        if ($userOrApiKey !== null && $password !== null){
            curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $userOrApiKey . ':' . $password);
        } elseif ($userOrApiKey !== null) {
            curl_setopt($this->ch, CURLOPT_PROXYUSERPWD, $userOrApiKey . ':' . rand(10000,90000));
        }
    }

    public function getResponseCode()
    {

        return $this->responseCode;
    }

    public function getResponseHeader()
    {

        return $this->responseHeader;
    }

    public function getResponseBody()
    {

        return $this->responseBody;
    }


    public function prepare($url, $method = null, $data = '')
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, true);

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

    public function setRequestContent($type)
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



    public function execute()
    {
        $response = curl_exec($this->ch);
        $this->responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $this->responseHeader = substr($response, 0, $header_size);
        $this->responseBody = substr($response, $header_size);
        curl_close($this->ch);

        return $this->responseBody;
    }
}
