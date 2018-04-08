<?php
/**
 * Rsa签名加密验证
 * sakmon 2016/08/11
 */
class Rsa
{
    /**
     * 生成签名
     * @param  [type] $data            [description]
     * @param  [type] $private_key_res [description]
     * @return [type]                  [description]
     */
	static public function sign($data , $private_key_res)
	{
		openssl_sign($data , $signature , openssl_pkey_get_private($private_key_res) , "sha1WithRSAEncryption");
		$sign = bin2hex( $signature ); //十六进制转换
		return $sign;
	}
    /**
     * 验证签名
     * @param  [type] $data           [description]
     * @param  [type] $sign           [description]
     * @param  [type] $public_key_res [description]
     * @return [type]                 [description]
     */
	static public function verify($data , $sign , $public_key_res)
	{
		$result = openssl_verify($data , hex2bin($sign) , openssl_pkey_get_public($public_key_res) , OPENSSL_ALGO_SHA1);
		if($result) return true;
		return false;
	}
    /**
     * 私钥加密
     * @param  [type] $data            [description]
     * @param  [type] $private_key_res [description]
     * @return [type]                  [description]
     */
    static public function privEncrypt($data , $private_key_res)
    {       
        return openssl_private_encrypt($data , $encrypted , openssl_pkey_get_private($private_key_res) )? base64_encode($encrypted) : null;
    }
    /**
     * 私钥解密
     * @param  [type] $encrypted       [description]
     * @param  [type] $private_key_res [description]
     * @return [type]                  [description]
     */
    static public function privDecrypt($encrypted , $private_key_res)
    {
    	return openssl_private_decrypt(base64_decode($encrypted) , $decrypted , openssl_pkey_get_private($private_key_res) , OPENSSL_PKCS1_PADDING)? $decrypted : null;
    }
    /**
     * 公匙加密
     * @param  [type] $data           [description]
     * @param  [type] $public_key_res [description]
     * @return [type]                 [description]
     */
    static public function pubEncrypt($data , $public_key_res) 
    {
        return openssl_public_encrypt($data , $encrypted , openssl_pkey_get_public($public_key_res) ) ? base64_encode($encrypted) : null;
    }
    /**
     * 公匙解密
     * @param  [type] $encrypted      [description]
     * @param  [type] $public_key_res [description]
     * @return [type]                 [description]
     */
    static public function pubDecrypt($encrypted , $public_key_res) 
    {
        return openssl_public_decrypt(base64_decode($encrypted) , $decrypted , openssl_pkey_get_public($public_key_res) , OPENSSL_PKCS1_PADDING)? $decrypted : null;
    }
}