<?php

require '../../vendor/autoload.php';
require 'config.php';

if(!session_id()) {
    session_start();
}

Podio::setup(CLIENT_ID, CLIENT_SECRET);
Podio::$debug = true;

// Just for testing, you don't really want to reset the auth_token everytime.
Podio::$oauth = new PodioOAuth();

if (!Podio::is_authenticated()) {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
}

$podioform = new \AdvancedWebform\AdvancedWebform(array(
	'app_id' => APP_ID,
	'item_id' => ITEM_ID,
	'lock_default' => false,
	'method' => 'post',
	'action' => '',
	'submit_value' => 'Skicka intresseanmälan',
));

// If you don't want to enable file upload
// $podioform->get_element('files')->set_attribute('hidden', true);

if ($_POST){
    try{
	$podioform->set_values($_POST, $_FILES);
	$podioform->save();
        
            $podioform = 'Thank you for your submission.';
    } catch (\AdvancedWebform\ElementError $e){
        $error_message = 'There\'s an error with the element.';
    } catch (\AdvancedWebform\CSRFError $e){
        $error_message = 'There\'s an error with the submission. Please revisit the form and submit again.';
        $podioform = '';
    } catch(\Exception $e){
        $error_message = 'There\'s an unknown error with the submission. Please revisit the form and submit again.';
        $podioform = '';
    }
}

require 'header.php';
require 'content.php';
require 'footer.php';