<?php

namespace CertCapture\Lib;

use CertCapture\Lib\SslHandshakeData;
use CertCapture\Lib\CertCaptureException;

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

    public function receive($client, $data)
    {
        var_dump(bin2hex($data));
        // todo maybe i can unpack packet by ssl type name and length
    }

    public function error($client)
    {
        throw new CertCaptureException("error");
    }

    public function close($client)
    {
        throw new CertCaptureException("server closed");
    }

}
