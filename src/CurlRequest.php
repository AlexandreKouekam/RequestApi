<?php

namespace ATEKA;

use ATEKA\TagWrapper;

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

    /**
     * @return mixed
     */
    public function getResponseHeader()
    {
        return $this->responseHeader;
    }

    /**
     * @param mixed $responseHeader
     */
    public function setResponseHeader($responseHeader)
    {
        $responseHeader = TagWrapper::TagPre($responseHeader);
        $this->responseHeader = $responseHeader;
    }

    /**
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @param mixed $responseBody
     */
    public function setResponseBody($responseBody)
    {
        $this->responseBody = $responseBody;
    }

    /**
     * @return mixed
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param mixed $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    public function getHeaderRow($row)
    {
        $accepted = [
                     'access-control-allow-credentials',
                     'access-control-allow-origin',
                     'content-type',
                     'last-modified',
                     'server',
                     'x-creationtime',
                     'expires',
                     'cache-control',
                     'pragma',
                     'date',
                     'content-length',
                     'location',
                     'connection',
                     'set-cookie',
        ];
        if (!in_array(strtolower($row), $accepted)) {

            return 'Error please row must contain '. implode(', ', $accepted);
        }

        $headers = explode(PHP_EOL, $this->getResponseHeader());
        foreach ($headers as $header) {
            $key = strtolower($header);
            if (strpos($key, $row) !== false) {

                return substr($header, strlen($row)+2);
            }
        }
        echo 'fail2';
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
        $this->setResponseHeader(substr($response, 0, $header_size));
        $this->setResponseBody(substr($response, $header_size));
        curl_close($this->ch);

        return $this->responseBody;
    }
}
