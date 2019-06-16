<?php

namespace CertCapture\HandshakeNode;

class CertificateNode extends Node
{
    public $certLength;

    public function __construct($data)
    {
        $vars = unpack("c1handshaketype/C3length/C3certlength", $data);

        $this->certLength = $vars['certlength1'] * pow(16, 4) +
            $vars['certlength2'] * pow(16, 2) +
            $vars['certlength3'];

        $this->subNodes['certifacatesNode'] = new CertificatesNode(substr($data, 7, $this->certLength));
    }
}
