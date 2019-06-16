<?php

namespace CertCapture\HandshakeNode;

abstract class Node
{
    public $subNodes = array();

    public function getSubNodes($name)
    {
        if ($name === null) {
            return null;
        }
        if (empty($this->subNodes[$name])) {
            return null;
        }
        return $this->subNodes[$name];
    }

    public function getNextChildData($length)
    {
        $data = 0;
        return $data;
    }
}
