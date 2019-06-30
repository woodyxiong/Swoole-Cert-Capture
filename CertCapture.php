<?php

namespace CertCapture;

use CertCapture\Lib\CertCaptureException;
use CertCapture\Lib\CertClient;
use Co;

class CertCapture
{
    public $targetIp;
    public $targetPort;
    public $targetHost;
    public $timeout;

    /**
     * CertCapture constructor.
     * @param $targetHost
     * @param null $targetIp
     * @param int $targetPort
     * @param int $timeout
     */
    public function __construct($targetHost, string $targetIp = "", int $targetPort = 443, float $timeout = 3)
    {
        $this->targetHost = $targetHost;
        $this->targetPort = $targetPort;
        $this->targetIp = empty($targetIp) ? gethostbyname($targetHost) : $targetIp;
        $this->timeout = $timeout;
    }

    /**
     * php_get_cert main()
     */
    public function getCert()
    {
        CertClient::flushStaticVars();

        $runException = new CertCaptureException();
        go(function () use (&$runException) {
            $certClient = new CertClient($this->targetIp, $this->targetPort, $this->targetHost, $this->timeout);
            try {
                $certClient->run();
            } catch (CertCaptureException $e) {
                $runException = $e;
                return;
            }
        });

        $startTime = microtime(true);
        $timeout = $this->timeout;
        $cert = "";
        $waitException = new CertCaptureException();

        go(function () use ($startTime, $timeout, &$cert, &$waitException) {
            try {
                while (true) {
                    Co::sleep(0.01);
                    $nowTime = microtime(true);

                    if (!empty(CertClient::$cert)) {
                        $cert = CertClient::$cert;
                        return;
                    }

                    if (($nowTime - $startTime) > $timeout) {
                        throw new CertCaptureException("time out");
                    }
                }
            } catch (CertCaptureException $e) {
                $waitException = $e;
            }

        });

        \swoole_event::wait();

        if (!empty($runException->getMessage())) {
            throw new CertCaptureException($runException->getMessage());
        }
        if (!empty($waitException->getMessage())) {
            throw new CertCaptureException($waitException->getMessage());
        }

        return $cert;
    }
}
