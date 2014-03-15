<?php

require '../vendor/autoload.php';
require 'config.php';
require '../PodioAdvancedForm.php';

Podio::setup(CLIENT_ID, CLIENT_SECRET);
Podio::$debug = true;


if (!Podio::is_authenticated()) {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
}

$podioform = new PodioAdvancedForm(array(
	'app_id' => APP_ID,
	'item_id' => null,
	'lock_default' => true,
	'method' => 'post',
	'action' => '',
	'submit_value' => 'Skicka intresseanmälan',
));

$podioform->get_element('files')->set_attribute('hidden', true);

if ($_POST){
    try{
	$podioform->set_values($_POST, $_FILES);
	$podioform->save();
        
            $podioform = 'Tack för din anmälan! Vi kommer att höra av oss inom kort.';
    } catch (PodioFormElementError $e){
        $error_message = 'There\'s an error with the element.';
    }
}

require 'header.php';
require 'content.php';
require 'footer.php';
