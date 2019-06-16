<?php

namespace CertCapture\HandshakeNode;

class Certificate extends Node
{
    public $cert;

    public function __construct($data)
    {
        $cert = '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split(base64_encode($data), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;
        $this->cert = $cert;
    }
}
