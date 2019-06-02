<?php

namespace CertCapture;

use CertCapture\Lib\CertClient;


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

    public function __construct($targetHost, $targetIp = null, $targetPort = 443)
    {
        $this->targetHost = $targetHost;
        $this->targetPort = $targetPort;
        $this->targetIp = empty($targetIp) ? gethostbyname($targetHost) : $targetIp;
    }

    public function getCert()
    {
        $certClient = new CertClient($this->targetIp, $this->targetPort, $this->targetHost);
        $certClient->getCert();
    }
}


//include 'Lib/CertClient.php';
require_once 'Lib/CertCaptureException.php';
require_once 'Lib/SslHandshakeData.php';
$certCapture = new CertCapture("ww1.sinaimg.cn", "218.92.152.11", 443);
$certCapture->getCert();
