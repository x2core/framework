<?php

namespace X2Core\Util;


use X2Core\Exceptions\RuntimeException;

class Hash
{
    /**
     * constants of class to helper with values
     */
    const SHA224 = 244;
    const SHA256 = 256;
    const SHA384 = 384;
    const SHA512 = 512;
    const RIPEMD128 = 128;
    const RIPEMD160 = 160;
    const RIPEMD256 = 256;
    const RIPEMD320 = 320;

    /**
     * @param $value
     * @param int $rounds
     * @param null $checkHash
     * @return bool|string
     * @throws RuntimeException
     */
    public static function bycrpt($value, $rounds = PASSWORD_BCRYPT_DEFAULT_COST, $checkHash = NULL){
        $result = password_hash($value, PASSWORD_BCRYPT, [
            'cost' => ['rounds' => $rounds],
        ]);

        if ($result === false) {
            throw new RuntimeException('Bcrypt hashing is not supported');
        }

        return $checkHash ? $result === $checkHash : $result;
    }

    /**
     * @param $value
     * @param int $domain
     * @param null $checkHash
     * @return bool|string
     */
    static public function literalbin($value, $domain = 256, $checkHash = NULL){
        $len = strlen($value) - 1;
        $amount = 0;
        for($i = 0; $i < $len; $i++){
            $curr = ord($value[$i]);
            $amount += ($domain*$curr)*($i+1);
        }
        $amount += ord($value[$len]);
        $amount += $len<<(6789*$len);
        $result = base_convert($amount, 10, 32);
        return $checkHash ? $result === $checkHash : $result;
    }

    /**
     * @param $value
     * @param int $length
     * @param null $checkValue
     * @return bool|string
     */
    static public function sha($value, $length = self::SHA256,  $checkValue = NULL)
    {
        $alg = "sha{$length}";
        $result = hash($alg, $value);
        return $checkValue ? $result === $checkValue : $result;
    }

    /**
     * @param $value
     * @param null $checkValue
     * @return bool|string
     */
    static public function md5($value,  $checkValue = NULL)
    {
        $result = hash('md5', $value);
        return $checkValue ? $result === $checkValue : $result;
    }

    /**
     * @param $value
     * @param int $length
     * @param null $checkValue
     * @return bool|string
     */
    static public function ripemd($value, $length = self::RIPEMD128,  $checkValue = NULL)
    {
        $alg = "ripemd{$length}";
        $result = hash($alg, $value);
        return $checkValue ? $result === $checkValue : $result;
    }

    /**
     * @param $value
     * @param null $checkValue
     * @return bool|string
     */
    static public function crc32b($value, $checkValue = NULL){
        $result = hash('crc32b', $value);
        return $checkValue ? $result === $checkValue : $result;
    }

}