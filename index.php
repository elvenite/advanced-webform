<?php

require 'vendor/autoload.php';
require 'config.php';
require 'lib/PodioAdvancedForm/PodioAdvancedForm.php';

Podio::setup(CLIENT_ID, CLIENT_SECRET);


if (!Podio::is_authenticated()) {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
}

$podioform = new PodioAdvancedForm(MONTER_APP_ID, array(
	'method' => 'post',
	'action' => 'http://podio.com',
	'enctype' => '',
));

require 'header.php';
require 'footer.php';
