<?php

namespace CertCapture\Test;

use CertCapture\CertCapture;
use CertCapture\Lib\CertCaptureException;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState         disabled
 */
class CertCaptureTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        defined('APP_PATH') or define('APP_PATH', __DIR__ . "/");
        spl_autoload_register('CertCapture\Test\CertCaptureTest::autoload');
    }

    public function testBaishancloud()
    {
        $certCapture = new CertCapture("www.baishancloud.com", "27.148.207.55", 443, 30);
        try {
            $bsCert = $certCapture->getCert();
        } catch (CertCaptureException $e) {
            var_dump($e->getMessage());
        }
        $certData = openssl_x509_parse($bsCert);
        $this->assertEquals("20465945815246023917054040487954357696", $certData['serialNumber']);
    }

    public function testBaidu()
    {
        $certCapture = new CertCapture("www.baidu.com", "", 443, 30);
        try {
            $baiduCert = $certCapture->getCert();
        } catch (CertCaptureException $e) {
            var_dump($e->getMessage());
        }

        $certData = openssl_x509_parse($baiduCert);
        $this->assertEquals("13905183944940287882518820211", $certData['serialNumber']);
    }

    public function testTimeOut()
    {
        $certCapture = new CertCapture("github.com", "", 443, 0.001);
        try {
            $githubCert = $certCapture->getCert();
        } catch (CertCaptureException $e) {
            $this->assertEquals("connect error", $e->getMessage());
        }
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
