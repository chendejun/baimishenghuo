<?php
/**
 * @Copyright: Copyright ©2012-2013 百米生活电子商务有限公司 All Rights Reserved
 * ---------------------------------------------
 * @desc: 字符加密
**/
class Encrypt {
    public $_key;                /* 加密的Key */
    public $_encryptString;       /* 加密以后的字符串 */
    public $_decryptString;       /* 解密以后的字符串 */
    public $_error;               /* 错误信息 */


    function __contructor (  ) {
        $this->_key = '';
    }




    /* 设置加密的Key */
    function setKey( $key ) {
        assert( is_string($key) );
        $this->_key = md5( $key );
    }


    /* 加密函数 */
    function encode( $content ){
        $this->_encryptString='';
        if ( empty( $this->_key ) ) {
            $this->_setErrMsg( 'run setKey() first.' );
            return false;
        }
        
        for ( $i = 0; $i < strlen( $content ); $i++ ) {
            $j = $i;
            if ( $j >= strlen( $this->_key ) ) {
                $j = 0;
            }
            $this->_encryptString .= $content[$i] ^ $this->_key[$j];
        }
        return urlencode(base64_encode($this->_encryptString));
    }


    /* 解密函数 */
    function decode( $content ){
        $this->_decryptString='';
        assert( is_string( $content ) );
        $content = base64_decode(urldecode( $content ));
        if ( empty( $this->_key ) ) {
            $this->_setErrMsg( 'run setKey() first.' );
            return false;
        }
        
        for ( $i = 0; $i < strlen( $content ); $i++ ) {
            $j = $i;
            if ( $j >= strlen( $this->_key ) ) {
                $j = 0;
            }
            $this->_decryptString .= $content[$i] ^ $this->_key[$j];
        }
        return $this->_decryptString;
    }


    function _setErrMsg( $msg ) {
        $this->_error .= $msg . '<br />';
    }
}

