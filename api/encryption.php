<?php

$key = "ypft^&uq^&ie8rn4917$0#";

function decrypt($data, $key) {
    return openssl_decrypt(base64_decode($data), "aes-128-ecb", $key, OPENSSL_RAW_DATA);
}

function encrypt($data, $key) {
    return base64_encode(openssl_encrypt($data, "aes-128-ecb", $key, OPENSSL_RAW_DATA));
}

$enc =  encrypt("http://myhostelapp.zingme.in/api/hosteler/auth.php",$GLOBALS['key']);
echo "Encrypted : ".$enc."</br>";
/*$dec = decrypt("Yhpw6me9y6Y8V8Ru7qELKO1oDw5gnYehm678rltYXa75NxHDH2XiE821O+Stv2Ev",$GLOBALS['key']);
echo "<br>Decrypted : ".$dec;*/
