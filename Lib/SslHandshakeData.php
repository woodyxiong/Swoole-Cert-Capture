<?php

namespace CertCapture\Lib;

use CertCapture\HandshakeNode\CertificateNode;

class SslHandshakeData
{
    /*
     * when server receive Client Hello
     * 1. send Server Hello
     * 2. send Certificate
     * 3. send Server Key Exchange
     * 4. send Server Hello Done
     */

    const HANDSHAKE_FLAG = 22;

    const HANDSHAKE_SERVER_HELLO = 2;
    const HANDSHAKE_CERTIFICATE = 11;

    public static function getClientHello($hostname)
    {
        $hexRecordLayerLength = self::dec2hex(strlen($hostname) + 312, 4);
        $hexClientHelloLength = self::dec2hex(strlen($hostname) + 308, 6);

        $serverNameLength = strlen($hostname) + 5;
        $hexServerNameLength = self::dec2hex($serverNameLength, 4);

        $hexServerNameListLength = self::dec2hex(strlen($hostname) + 3, 4);

        $hexLength = self::dec2hex(strlen($hostname), 4);
        $hexHostname = self::str2hex($hostname);

        $hexExtensionLength = self::dec2hex(strlen($hostname) + 94, 4);

        $data = "160301{$hexRecordLayerLength}01{$hexClientHelloLength}030395f6ea4e0d21cbc9eeefa5d5c4ac2a044ac7a5c7343b20f3f3db8d050c10625d0000acc030c02cc028c024c014c00a00a500a300a1009f006b006a0069006800390038003700360088008700860085c032c02ec02ac026c00fc005009d003d00350084c02fc02bc027c023c013c00900a400a200a0009e00670040003f003e0033003200310030009a0099009800970045004400430042c031c02dc029c025c00ec004009c003c002f009600410007c011c007c00cc00200050004c012c008001600130010000dc00dc003000a00ff020100{$hexExtensionLength}0000{$hexServerNameLength}{$hexServerNameListLength}00{$hexLength}{$hexHostname}000b000403000102000a001c001a00170019001c001b0018001a0016000e000d000b000c0009000a00230000000d0020001e060106020603050105020503040104020403030103020303020102020203000f000101";
        return hex2bin($data);
    }

    /**
     * convert decimal to hex and fill with zero
     * @param $dec
     * @param $length
     * @return string
     */
    private static function dec2hex($dec, $length)
    {
        // todo judge dec can saved in such length
        $hexStr = dechex($dec);
        $retStr = "";
        if (strlen(strlen($hexStr) < $length)) {
            $fillNum = $length - strlen($hexStr);
            for ($i = 0; $i < $fillNum; $i++) {
                $retStr = $retStr . "0";
            }
        }
        return $retStr . $hexStr;
    }

    /**
     * convert string to hex
     * @param $str
     * @return string
     */
    private static function str2hex($str)
    {
        return bin2hex($str);
    }

    /**
     * parse data to cert
     * @param $data
     * @return array
     * @throws CertCaptureException
     */
    public static function parseCert(&$data)
    {
        while (1) {
            // if data length is not enough to parse, return
            if (strlen($data) < 6) {
                return [false, ""];
            }

            $vars = unpack("c1type/n1version/n1length/c1handshake_type", $data);
            if (strlen($data) - 5 < $vars['length']) {
                return [false, ""];
            }

            if (!in_array($vars['handshake_type'], [self::HANDSHAKE_CERTIFICATE,
                self::HANDSHAKE_SERVER_HELLO])) {
                throw new CertCaptureException("handshake type error");
            }

            if ($vars['handshake_type'] == self::HANDSHAKE_CERTIFICATE) {
                $node = new CertificateNode(substr($data, 5, $vars['length']));

                return [
                    true,
                    $node->getSubNodes('certifacatesNode')
                        ->getSubNodes('certificates')[0]
                        ->cert
                ];
            }

            // cut packet
            $data = substr($data, $vars['length'] + 5);
            return [false, ""];
        }

        return [false, ""];
    }
}
