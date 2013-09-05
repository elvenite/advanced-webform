<?php

require 'vendor/autoload.php';
require 'config.php';
require 'lib/PodioAdvancedForm/PodioAdvancedForm.php';

Podio::setup(CLIENT_ID, CLIENT_SECRET);

Podio::$debug = true;

// reset access token
Podio::$oauth = new PodioOAuth();


if (!Podio::is_authenticated()) {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
}

$podioform = new PodioAdvancedForm(array(
	'app_id' => 4583624,
	'app' => '', // insert a PodioApp object if you want
	'item_id' => 73691824, // prefill the form with values from an item,
						   // the item will be updated
	'item' => '', // insert a PodioItem object if you want
	'method' => 'post',
	'action' => '',
	'submit_value' => 'Boka monter!',
));

$podioform->get_element('paket2')->set_attribute('locked', true);


if ($_POST){
	$podioform->set_values($_POST, $_FILES);
	if (!$podioform->save()){
		$error_message = $podioform->get_error();
	}
}

require 'header.php';
require 'content.php';
require 'footer.php';
