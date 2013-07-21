<?php

require 'vendor/autoload.php';
require 'config.php';
require 'lib/PodioAdvancedForm/PodioAdvancedForm.php';

Podio::setup(CLIENT_ID, CLIENT_SECRET);

Podio::$debug = true;


if (!Podio::is_authenticated()) {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
}

$podioform = new PodioAdvancedForm(array(
	'app_id' => MONTER_APP_ID,
	'app' => '', // insert a PodioApp object if you want
	'item_id' => 64389008,
	'item' => '', // insert a PodioItem object if you want
	'method' => 'post',
	'action' => '',
	'enctype' => '',
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
