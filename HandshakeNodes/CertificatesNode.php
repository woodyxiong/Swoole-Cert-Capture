<?php

namespace CertCapture\HandshakeNode;

class CertificatesNode extends Node
{
    public $certsLength = [];

    public function __construct($data)
    {
        $this->subNodes['certificates'] = [];
        while (1) {
            $vars = unpack("C3certlength", $data);

            $certLength = $vars['certlength1'] * pow(16, 4) +
                $vars['certlength2'] * pow(16, 2) +
                $vars['certlength3'];
            $this->certsLength[] = $certLength;
            $this->subNodes['certificates'][] = new Certificate(substr($data, 3, $certLength));
            $data = substr($data, 3 + $certLength);

            if (strlen($data) <= 0) {
                return;
            }
        }
    }
}