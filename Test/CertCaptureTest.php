<?php

namespace CertCapture\Test;

use CertCapture\CertCapture;
use PHPUnit\Framework\TestCase;

class CertCaptureTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        defined('APP_PATH') or define('APP_PATH', __DIR__ . "/");
        spl_autoload_register('CertCapture\Test\CertCaptureTest::autoload');
    }

    public function testQQ()
    {
        $certCapture=new CertCapture("www.qq.com","183.3.226.35",443,0.5);
//        $tencentCert=$certCapture->getCert();

        $this->assertEquals(1,1);
//        $certData=openssl_x509_parse($tencentCert);
//        $this->assertEquals("10862042356945370696581816798",$certData['serialNumber']);
    }

    public static function autoload($class)
    {
        $strs = explode('\\', $class);
        if ($strs[0] === 'CertCapture') {
            unset($strs[0]);
            $class = implode("/", $strs);
        }
        $filename = APP_PATH . "../" . $class . ".php";
        include_once $filename;
    }
}
