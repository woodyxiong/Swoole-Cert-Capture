<?php

class CertCapture {
    public $targetIp;
    public $targetPort;
    public $targetHost;

    public function __construct($targetHost,$targetIp=null,$targetPort=443)
    {
        $this->targetHost=$targetHost;
        $this->targetPort=$targetPort;
        $targetIp = empty($targetIp) ? gethostbyname($targetHost) : $targetIp;
    }

    public function getCert()
    {

    }
}
