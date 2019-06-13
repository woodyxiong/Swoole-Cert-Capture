<?php

namespace CertCapture;

use CertCapture\Lib\CertCaptureException;
use CertCapture\Lib\CertClient;
use Co;


spl_autoload_register(function ($class) {
    $strs = explode('\\', $class);
    $filename = $strs[1] . '/' . $strs[2] . '.php';
    include_once $filename;
});


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
     * @throws Lib\CertCaptureException
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

//include 'Lib/CertClient.php';
require_once 'Lib/CertCaptureException.php';
require_once 'Lib/SslHandshakeData.php';
require_once 'HandshakeNodes/Node.php';
require_once 'HandshakeNodes/CertificateNode.php';
require_once 'HandshakeNodes/CertificatesNode.php';
require_once 'HandshakeNodes/Certificate.php';
$certCapture = new CertCapture("ww1.sinaimg.com", "218.92.152.11", 443, 3);
//$certCapture = new CertCapture("www.qiniu.com", "218.92.152.11", 443);
$cert = $certCapture->getCert();
var_dump($cert);
