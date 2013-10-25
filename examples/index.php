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
	'app_id' => 5421613,
	'lock_default' => true,
	'method' => 'post',
	'action' => '',
	'submit_value' => 'I wanna come!',
));

if ($_POST){
	$podioform->set_values($_POST, $_FILES);
	if (!$podioform->save()){
		$error_message = $podioform->get_error();
	}
}

require 'header.php';
require 'content.php';
require 'footer.php';
