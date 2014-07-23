<?php

/**
 * Advanced Webform for Podio - A form generator for Podio
 *
 * @author      Carl-Fredrik Herö <carl-fredrik.hero@elvenite.se>
 * @copyright   2014 Carl-Fredrik Herö
 * @link        https://github.com/elvenite/advanced-webform
 * @license     https://github.com/elvenite/advanced-webform
 * @version     1.0.0
 * @package     AdvancedWebform
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace AdvancedWebform;


/**
 * CSRF support
 * Lots of inspiration from https://github.com/BKcore/NoCSRF
 * @package AdvancedWebform
 * @author  Carl-Fredrik Herö
 * @since   1.0.0
 */
class CSRF {
    protected $_token_name = 'advanced-webform-csrf';
    
    public function __construct() {
        if(!session_id()) {
          session_start();
        }
    }


    public function generate($key = null){
        if (null === $key){
            $key = $this->_token_name;
        }
      
        $origin = sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] );
        // token generation (basically base64_encode any random complex string, time() is used for token expiration) 
        $token = base64_encode( time() . $origin . self::randomString( 32 ) );
        // store the one-time token in session
        $_SESSION[ $key ] = $token;
        
        return $token;
      
    }
  
    public function is_valid( $data, $key = null, $timespan = null ) {
        if (null === $key){
          $key = $this->_token_name;
      }
        if ( !isset( $_SESSION[ $key ] ) ) {
            throw new \AdvancedWebform\CSRFError( 'Missing CSRF session token.' );
        }

        // Get valid token from session
        $hash = $_SESSION[ $key ];

        // Free up session token for one-time CSRF token usage.
        $_SESSION[ $key ] = null;

        // Origin checks
        if(  sha1( $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] ) != substr( base64_decode( $hash ), 10, 40 ) )
        {
            throw new \AdvancedWebform\CSRFError( 'Form origin does not match token origin.' );
        }
        
        // Check if session token matches form token
        if ( $data != $hash ) {
            throw new \AdvancedWebform\CSRFError( 'Invalid CSRF token.' );
        }

        // Check for token expiration
        if ( $timespan != null && is_int( $timespan ) && intval( substr( base64_decode( $hash ), 0, 10 ) ) + $timespan < time() ) {
            throw new \AdvancedWebform\CSRFError( 'CSRF token has expired.' );
        }


        return true;
    }
  
  /**
     * Generates a random string of given $length.
     *
     * @param Integer $length The string length.
     * @return String The randomly generated string.
     */
    protected function randomString( $length = 32 )
    {
        $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max = strlen( $seed ) - 1;

        $string = '';
        for ( $i = 0; $i < $length; ++$i ) {
            $string .= $seed{intval( mt_rand( 0.0, $max ) )};
        }

        return $string;
    }
}
