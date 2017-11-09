<?php
/*
* To use this class...your php version must be
* v 5.5 or higher
* admin : iamlegend@1
*/
use core\libs\Database;

defined('ACCESS') || AppError::exitApp();

/**
*
*/
class Authenticator extends FuniObject
{
	private $_db;

	function __construct(Database $db = null)
	{
		# code...
		if(!is_null($db)){
			$this->set('db', $db);
		}

		# return object to allow method chaining
		return $this;
	}

	public function hashPassword($pwd='')
	{
		# use default crytpt function & cost
		return password_hash($pwd, PASSWORD_DEFAULT);
	}

	public function verifyPassword($data = array())
	{


	}

	/**
	 * Encrypt and decrypt
	 *
	 * @author Nazmul Ahsan <n.mukto@gmail.com>
	 * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
	 *
	 * @param string $string string to be encrypted/decrypted
	 * @param string $action what to do with this? e for encrypt, d for decrypt
	 */
	public static function simpleCrypt( $string, $action = 'e' )
	{

        $secret_key = 'hApPYwiFEHaPpyL1f3';
        $secret_iv = 'SYz@A+min0_0';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

        return $output;

	}


}
