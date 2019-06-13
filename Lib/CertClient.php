<?php

namespace CertCapture\Lib;

use Swoole\Coroutine\Client;

class CertClient
{
    public static $isGetCert = false;
    public static $cert = "";

    public $client;
    public $ip;
    public $port;
    public $host;
    public $timeout;

    public $serverHelloData = "";

    /**
     * static get cert when cert is exist
     * @return string
     * @throws CertCaptureException
     */
    public static function getCert()
    {
        if (self::$isGetCert === true) {
            $cert = self::$cert;
            self::$cert = "";
            return $cert;
        }
        return "";
    }


    public static function flushStaticVars()
    {
        self::$isGetCert = false;
        self::$cert = "";
    }

    /**
     * generate certClient instance
     * @param $ip
     * @param $port
     * @param $host
     * @param float $timeout
     */
    public function __construct($ip, $port, $host, float $timeout)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->host = $host;
        $this->timeout = $timeout;

        $this->client = new Client(SWOOLE_TCP);
    }

    /**
     * let's start run the certClient
     * @throws CertCaptureException
     */
    public function run()
    {
        if (!$this->client->connect($this->ip, $this->port, $this->timeout)) {
            throw new CertCaptureException("connect error");
        }

        $data = SslHandshakeData::getClientHello($this->host);
        $this->client->send($data);

        while (!self::$isGetCert) {
            $data = $this->client->recv();
            if ($data === "") {
                throw new CertCaptureException("server closed");
            }
            $this->receive($data);
        }
    }

    /**
     * receive and parse when detect handshake packet
     * @param $client
     * @param $data
     * @throws \CertCapture\Lib\CertCaptureException
     */
    private function receive($data)
    {
        $this->serverHelloData .= $data;
        [$isGetCert, $cert] = SslHandshakeData::parseCert($this->serverHelloData);
        if ($isGetCert) {
            self::$isGetCert = true;
            self::$cert = $cert;

            $this->client->close();
        }
    }
}
