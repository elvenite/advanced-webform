<?php

require '../../vendor/autoload.php';
require '../../config.php';
require 'PodioAdvancedForm.php';

Podio::setup(CLIENT_ID, CLIENT_SECRET);

Podio::$debug = true;


if (!Podio::is_authenticated()) {
  Podio::authenticate('password', array('username' => USERNAME, 'password' => PASSWORD));
}

$podioform = new PodioAdvancedForm(array(
	'app_id' => MONTER_APP_ID,
	'app' => '', // insert a PodioApp object if you want
	'item_id' => null, // prefill the form with values from an item,
						   // the item will be updated
	'item' => '', // insert a PodioItem object if you want
	'method' => 'post',
	'action' => '',
));

$podioform->mode = 'admin';

?><!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Podio Advanced Form Admin</title>
		<link rel="stylesheet" href="../../css/bootstrap.min">
		<style>
			.hover{
				background: #ffc !important;
			}
			.required{
				color: #f00;
			}
			
			.form-actions{
				display: none;
			}
		</style>
	</head>
<body>
	
	<div id="main" class="container">
		<div class="row">
			<div class="span12">
				<h1>Podio Advanced Form Admin</h1>
			</div>
		</div>
		
		<div class="row">
			<div class="span12">
				<?php echo $podioform; ?>
			</div>
		</div>
	</div> <!-- #main.container -->
	
	<script type="text/javascript" src="admin/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="admin/js/underscore.js"></script>
	<script type="text/javascript" src="admin/js/backbone.js"></script>
	<script type="text/javascript" src="admin/js/paf.js"></script>
</body>
</html>