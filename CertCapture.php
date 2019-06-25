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

    public function __construct($targetHost, $targetIp = null, $targetPort = 443, $timeout = 3)
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

        go(function () {
            $certClient = new CertClient($this->targetIp, $this->targetPort, $this->targetHost, $this->timeout);
            $certClient->run();
        });

        $startTime = microtime(true);
        $timeout = $this->timeout;

        $cert = "";

        go(function () use ($startTime, $timeout, &$cert) {
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
        });

        \swoole_event::wait();
        return $cert;
    }
}
