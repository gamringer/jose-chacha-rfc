<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Base64Url\Base64Url;



$msg = 'Hello World!';
/*
$jwe = (new \gamringer\rfc\example\Crypt($msg))->__toString();

echo '--------------------------------', PHP_EOL;
echo 'jwe          : ', $jwe, PHP_EOL;
*/

$kp = sodium_crypto_box_keypair();
$recipient = new \gamringer\rfc\example\Peer($kp);
$jwe = (new \gamringer\rfc\example\CryptECDH($msg, $recipient))->__toString();

echo '--------------------------------', PHP_EOL;
echo 'jwe          : ', $jwe, PHP_EOL;
/*
echo '================================', PHP_EOL;
$decryptor = new \gamringer\Aries\Decrypt($recipient);
$payload = $decryptor->decrypt((string)$jwe, $sender);
echo $payload, PHP_EOL;
*/
