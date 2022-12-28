<?php

namespace core\base\models;

use core\base\controllers\Singleton;

class Crypt
{

    use Singleton;

    private $cryptMethod = 'AES-128-CBC';
    private $hashAlgoritm = 'sha256';
    private $hashLength = 32;

    public function encrypt($str){

        $ivlen = openssl_cipher_iv_length($this->cryptMethod);

        $iv = openssl_random_pseudo_bytes($ivlen);

        $cipherText = openssl_encrypt($str , $this->cryptMethod, CRYPT_KEY, OPENSSL_RAW_DATA, $iv);

        $hmac = hash_hmac($this->hashAlgoritm , $cipherText , CRYPT_KEY ,true);

        return $this->cryptCombine($cipherText, $iv, $hmac);

    }

    public function decrypt($str){

        $ivlen = openssl_cipher_iv_length($this->cryptMethod);

        $crypt_data = $this->cryptUnCombine($str , $ivlen);

        $original_plaintext = openssl_decrypt($crypt_data['str'] , $this->cryptMethod, CRYPT_KEY , OPENSSL_RAW_DATA , $crypt_data['iv']);

        $calcmac = hash_hmac($this->hashAlgoritm , $crypt_data['str'], CRYPT_KEY , true);

        if(hash_equals($crypt_data['hmac'] , $calcmac)) return $original_plaintext;

        return false;

    }

    protected function cryptCombine($str , $iv , $hmac){

        $new_str = '';

        $strlen = strlen($str);

        $counter = (int)ceil(strlen(CRYPT_KEY) / ($strlen + $this->hashLength));

        $progress = 1;

        if($counter >= $strlen) $counter =1;

        for ($i = 0; $i< $strlen; $i++){

            if($counter < $strlen){

                if($counter === $i){

                    $new_str.= substr($iv , $progress-1,1);
                    $progress++;
                    $counter += $progress;

                }

            }else{

                break;

            }

            $new_str .=substr($str , $i ,1);

        }

        $new_str .= substr($str , $i);
        $new_str .= substr($iv, $progress - 1);

        $new_str = substr($new_str , 0 , (int)ceil(strlen($new_str) / 2)) . $hmac . substr($new_str , (int)ceil(strlen($new_str) / 2));

        return base64_encode($new_str);

    }

    protected function cryptUnCombine($str , $ivlen){

        $crypt_data = [];

        $str = base64_decode($str);

        $hash_position = (int)ceil(strlen($str) / 2 - $this->hashLength /2);

        $crypt_data['hmac'] = substr($str , $hash_position , $this->hashLength);

        $str = str_replace($crypt_data['hmac'] , '', $str);

        $counter = (int)ceil(strlen(CRYPT_KEY) / (strlen($str) - $ivlen + $this->hashLength));

        $progress = 2;

        $crypt_data['str'] = '';

        for($i = 0; $i< strlen($str); $i++){

            if($ivlen + strlen($crypt_data['str']) <strlen($str)){

                if($i === $counter){

                    $crypt_data['iv'] .= substr($str , $counter , 1);
                    $progress++;
                    $counter += $progress;

                }else{

                    $crypt_data['str'] .=substr($str , $i ,1);

                }

            }else{

                $crypt_data_len = strlen($crypt_data['str']);

                $crypt_data['str'] .= substr($str , $i, strlen($str) - $ivlen  - $crypt_data_len);
                $crypt_data['iv'] .=substr($str , $i +(strlen($str) - $ivlen  - $crypt_data_len));

                break;

            }

        }

        return $crypt_data;
    }

}