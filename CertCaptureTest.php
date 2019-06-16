<?php

namespace CertCapture;

class CertCaptureTest
{
    public static function autoload($class)
    {
        $strs = explode('\\', $class);
        if ($strs[0] === 'CertCapture') {
            unset($strs[0]);
            $class = implode("/", $strs);
        }
        $filename = APP_PATH . "./" . $class . ".php";
        include_once $filename;
    }

    public function testCertCapture()
    {
        defined('APP_PATH') or define('APP_PATH', __DIR__ . "/");
        spl_autoload_register('CertCapture\CertCaptureTest::autoload');

        $certCapture = new CertCapture("ww1.sinaimg.com", "218.92.152.11", 443, 3);
        //$certCapture = new CertCapture("www.qiniu.com", "218.92.152.11", 443);
        $cert = $certCapture->getCert();
        var_dump($cert);
    }
}

$certCaptureTest = new CertCaptureTest();
$certCaptureTest->testCertCapture();
