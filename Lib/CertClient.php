<?php

namespace CertCapture\Lib;

class CertClient
{
    public $client;
    public $ip;
    public $port;
    public $host;
    public $timeout;

    public $serverHelloData = "";

    public function __construct($ip, $port, $host, $timeout = 0.5)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->host = $host;
        $this->timeout = $timeout;

        $this->client = new \swoole_client(SWOOLE_TCP | SWOOLE_ASYNC); //async,no-block
        $this->client->on('connect', array($this, 'connect'));
        $this->client->on('receive', array($this, 'receive'));
        $this->client->on('close', array($this, 'close'));
        $this->client->on('error', array($this, 'error'));
    }

    /**
     * start client
     * @throws \CertCapture\Lib\CertCaptureException
     */
    public function getCert()
    {
        if (!$this->client->connect($this->ip, $this->port, $this->timeout)) {
            throw new CertCaptureException("connect error");
        }
    }

    public function connect($client)
    {
        $data = SslHandshakeData::getClientHello($this->host);
        $client->send($data);
    }

    /**
     * receive and parse when detect handshake packet
     * @param $client
     * @param $data
     * @throws \CertCapture\Lib\CertCaptureException
     */
    public function receive($client, $data)
    {
        $this->serverHelloData .= $data;
        [$isGetCert, $cert] = SslHandshakeData::parseCert($this->serverHelloData);
        if ($isGetCert) {
            echo $cert;
        }
    }

    /**
     * error
     * @param $client
     * @throws \CertCapture\Lib\CertCaptureException
     */
    public function error($client)
    {
        throw new CertCaptureException("error");
    }

    /**
     * server closed
     * @param $client
     * @throws \CertCapture\Lib\CertCaptureException
     */
    public function close($client)
    {
        throw new CertCaptureException("server closed");
    }

}
