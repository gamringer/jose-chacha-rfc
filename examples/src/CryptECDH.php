<?php

declare(strict_types=1);

namespace gamringer\rfc\example;

use Jose\Component\Encryption\Algorithm\KeyEncryption\Util\ConcatKDF;
use Base64Url\Base64Url;

class CryptECDH
{
    protected $base58;
    protected $payload;
    protected $recipient;
    protected $kek;
    protected $output;

    public function __construct(string $payload, Peer $recipient)
    {
        $this->base58 = new \StephenHill\Base58();
        $this->payload = $payload;
        $this->recipient = $recipient;
    }

    public function encode()
    {
        if (isset($this->output)) {
            return $this->output;
        }

        $kp = sodium_crypto_box_keypair();
        $sender = new \gamringer\rfc\example\Peer($kp);
        $apu = Base64Url::encode(random_bytes(64));

        $symkey = \sodium_crypto_aead_xchacha20poly1305_ietf_keygen();

        $Z = sodium_crypto_scalarmult($this->recipient->getPrivateKey(), $sender->getPublicKey());
        $kek = $this->concatKDF($Z, $apu);
        $keyIV = random_bytes(\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $keOutput = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($this->payload, '', $keyIV, $kek);
        $keyTag = substr($keOutput, -\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_ABYTES);
        $keyEncrypted = substr($keOutput, 0, -\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_ABYTES);

        $headers = [
            "alg" => "ECDH-ES+XC20PKW",
            "enc" => "XC20P",
            "iv" => Base64Url::encode($keyIV),
            "tag" => Base64Url::encode($keyTag),
            "apu" => Base64Url::encode($apu),
            'epk' => [
                'typ' => 'OKP',
                'crv' => 'X25519',
                'x' => Base64Url::encode($sender->getPublicKey()),
            ],
        ];
        $headersEncoded = Base64Url::encode(json_encode($headers));

        $symaad = $headersEncoded;
        $nonce = random_bytes(\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_NPUBBYTES);
        $symoutput = sodium_crypto_aead_xchacha20poly1305_ietf_encrypt($this->payload, $symaad, $nonce, $symkey);
        $tag = substr($symoutput, -\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_ABYTES);
        $ciphertext = substr($symoutput, 0, -\SODIUM_CRYPTO_AEAD_XCHACHA20POLY1305_IETF_ABYTES);

        echo 'Payload        : ' . Base64Url::encode($this->payload), PHP_EOL;
        echo '--------------------------------', PHP_EOL;
        echo 'Ephemeral SK   : ' . Base64Url::encode($sender->getPrivateKey()), PHP_EOL;
        echo 'Recipient PK   : ' . Base64Url::encode($this->recipient->getPublicKey()), PHP_EOL;
        echo 'Z              : ' . Base64Url::encode($Z), PHP_EOL;
        echo 'APU            : ' . Base64Url::encode($apu), PHP_EOL;
        echo '--------------------------------', PHP_EOL;
        echo 'KEK            : ' . Base64Url::encode($kek), PHP_EOL;
        echo 'KE Nonce       : ' . Base64Url::encode($keyIV), PHP_EOL;
        echo 'KE Tag         : ' . Base64Url::encode($keyTag), PHP_EOL;
        echo 'KE Ciphertext  : ' . Base64Url::encode($keyEncrypted), PHP_EOL;
        echo 'Headers        : ' . json_encode($headers), PHP_EOL;
        echo 'Encoded Headers: ' . $headersEncoded, PHP_EOL;
        echo '--------------------------------', PHP_EOL;
        echo 'CEK            : ' . Base64Url::encode($symkey), PHP_EOL;
        echo 'CE AAD         : ' . $symaad, PHP_EOL;
        echo 'CE Nonce       : ' . Base64Url::encode($nonce), PHP_EOL;
        echo 'CE Tag         : ' . Base64Url::encode($tag), PHP_EOL;
        echo 'CE Ciphertext  : ' . Base64Url::encode($ciphertext), PHP_EOL;

        return [
        	"protected" => $headersEncoded,
        	"encrypted_key" => Base64Url::encode($keyEncrypted),
        	"iv" => Base64Url::encode($nonce),
        	"ciphertext" => Base64Url::encode($ciphertext),
        	"tag" => Base64Url::encode($tag),
        ];
    }

    private function concatKDF($Z, $apu)
    {
        return ConcatKDF::generate($Z, 'XC20P', 256, $apu);
    }

    public function __toString()
    {
    	$encoded = $this->encode();

        return implode('.', [
        	$encoded['protected'],
        	$encoded['encrypted_key'],
        	$encoded['iv'],
        	$encoded['ciphertext'],
        	$encoded['tag'],
        ]);
    }
}
