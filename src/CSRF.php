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

    public function generate($secret, $key = null, $timestamp = null){
        $secret = (string) $secret;
        
        if (null === $key){
            $key = $this->_token_name;
        }
        
        if ( null === $timestamp ){
            $timestamp = (string) date('YmdHis');
        }
      
        $origin = $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
        
        // token generation (basically base64_encode any random complex string, time() is used for token expiration) 
        $token = $timestamp . sha1( $timestamp . $origin . $secret );
        
        return $token;
    }
  
    public function is_valid( $data, $secret, $key = null ) {
        $secret = (string) $secret;
        
        if (null === $key){
          $key = $this->_token_name;
        }

        $timestamp = (string) substr($data, 0, 14);
        
        $token = $this->generate($secret, $key, $timestamp);

        // Check if token === $data
        if ( $token !== $data ) {
            throw new \AdvancedWebform\CSRFError( 'Invalid CSRF token.' );
        }

        return true;
    }
}